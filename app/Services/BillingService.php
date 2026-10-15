<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentItem;
use App\Models\Plan;
use App\Models\Store;
use App\Models\StoreCredit;
use App\Models\StoreSubscription;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class BillingService
{
    public const INTENT_ADD_STORE = 'add_store';

    public const INTENT_EXTEND = 'extend';

    public function submitInvoice(
        User $user,
        string $intent,
        Plan $plan,
        array $storeIds,
        string $method,
        string $reference,
        UploadedFile $screenshot,
        ?string $ownerNote = null,
    ): Payment {
        if ($user->hasPendingPayment()) {
            throw ValidationException::withMessages([
                'payment' => 'You already have a payment waiting for review.',
            ]);
        }

        if ($intent === self::INTENT_ADD_STORE) {
            $plan = Plan::pro() ?? $plan;
        }

        if (! $plan->isPaid() || ! $plan->is_active) {
            throw ValidationException::withMessages([
                'plan_id' => 'The selected plan is invalid.',
            ]);
        }

        $storeIds = array_values(array_unique(array_map('intval', $storeIds)));
        $ownedIds = $user->stores()->pluck('id')->all();
        $invalid = array_diff($storeIds, $ownedIds);

        if ($invalid !== []) {
            throw ValidationException::withMessages([
                'store_ids' => 'One or more selected stores are invalid.',
            ]);
        }

        if ($intent === self::INTENT_EXTEND && $storeIds === []) {
            throw ValidationException::withMessages([
                'store_ids' => 'Select at least one store to extend.',
            ]);
        }

        $lineCount = $intent === self::INTENT_ADD_STORE ? 1 : count($storeIds);
        $amount = $plan->price_mmk * $lineCount;

        return DB::transaction(function () use ($user, $intent, $storeIds, $method, $reference, $screenshot, $ownerNote, $plan, $amount) {
            $path = $screenshot->store('payment_screenshots', 'public');

            $payment = Payment::query()->create([
                'user_id' => $user->id,
                'amount_mmk' => $amount,
                'status' => Payment::STATUS_PENDING,
                'method' => $method,
                'reference' => $reference,
                'screenshot_path' => $path,
                'owner_note' => $ownerNote,
            ]);

            if ($intent === self::INTENT_ADD_STORE) {
                PaymentItem::query()->create([
                    'payment_id' => $payment->id,
                    'type' => PaymentItem::TYPE_NEW_STORE_SLOT,
                    'store_id' => null,
                    'plan_id' => $plan->id,
                    'price_mmk' => $plan->price_mmk,
                    'duration_days' => $plan->duration_days,
                ]);
            } else {
                foreach ($storeIds as $storeId) {
                    PaymentItem::query()->create([
                        'payment_id' => $payment->id,
                        'type' => PaymentItem::TYPE_STORE_RENEWAL,
                        'store_id' => $storeId,
                        'plan_id' => $plan->id,
                        'price_mmk' => $plan->price_mmk,
                        'duration_days' => $plan->duration_days,
                    ]);
                }
            }

            return $payment->load('items');
        });
    }

    public function approve(Payment $payment, User $reviewer, ?string $adminNote = null): void
    {
        if (! $payment->isPending()) {
            throw ValidationException::withMessages([
                'payment' => 'This payment has already been reviewed.',
            ]);
        }

        DB::transaction(function () use ($payment, $reviewer, $adminNote) {
            $payment->load('items');

            foreach ($payment->items as $item) {
                if ($item->type === PaymentItem::TYPE_STORE_RENEWAL && $item->store) {
                    $this->applyPaidPeriod($item->store, $item->plan, $item->duration_days, $item->price_mmk, $payment);
                    if (! $item->store->is_active) {
                        $item->store->update(['is_active' => true]);
                    }
                }

                if ($item->type === PaymentItem::TYPE_NEW_STORE_SLOT) {
                    StoreCredit::query()->create([
                        'user_id' => $payment->user_id,
                        'payment_id' => $payment->id,
                        'payment_item_id' => $item->id,
                        'plan_id' => $item->plan_id,
                        'duration_days' => $item->duration_days,
                    ]);
                }
            }

            $payment->update([
                'status' => Payment::STATUS_APPROVED,
                'admin_note' => $adminNote,
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
            ]);
        });
    }

    public function reject(Payment $payment, User $reviewer, ?string $adminNote = null): void
    {
        if (! $payment->isPending()) {
            throw ValidationException::withMessages([
                'payment' => 'This payment has already been reviewed.',
            ]);
        }

        $payment->update([
            'status' => Payment::STATUS_REJECTED,
            'admin_note' => $adminNote,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
        ]);
    }

    public function attachFreeSubscription(Store $store): StoreSubscription
    {
        $plan = Plan::free();

        if (! $plan) {
            throw new RuntimeException('The Free plan is not configured.');
        }

        $this->cancelActiveSubscriptions($store);

        return StoreSubscription::query()->create([
            'store_id' => $store->id,
            'plan_id' => $plan->id,
            'status' => StoreSubscription::STATUS_ACTIVE,
            'starts_at' => now(),
            'ends_at' => null,
            'price_mmk' => 0,
        ]);
    }

    public function consumeCredit(User $user, Store $store): StoreSubscription
    {
        $credit = $user->unusedStoreCredits()->with('plan')->oldest('id')->first();

        if (! $credit) {
            throw ValidationException::withMessages([
                'store' => 'Buy a plan to add another store.',
            ]);
        }

        return DB::transaction(function () use ($credit, $store) {
            $subscription = $this->applyPaidPeriod(
                $store,
                $credit->plan,
                $credit->duration_days,
                $credit->plan->price_mmk,
                $credit->payment,
            );

            $credit->update([
                'consumed_at' => now(),
                'consumed_store_id' => $store->id,
            ]);

            return $subscription;
        });
    }

    public function provisionNewStore(User $user, Store $store): StoreSubscription
    {
        $hasOtherStores = $user->stores()->whereKeyNot($store->id)->exists();

        if (! $hasOtherStores) {
            return $this->attachFreeSubscription($store);
        }

        return $this->consumeCredit($user, $store);
    }

    public function expireDueSubscriptions(): int
    {
        $due = StoreSubscription::query()
            ->with('store')
            ->where('status', StoreSubscription::STATUS_ACTIVE)
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', now())
            ->get();

        $count = 0;

        foreach ($due as $subscription) {
            DB::transaction(function () use ($subscription, &$count) {
                $subscription->update(['status' => StoreSubscription::STATUS_EXPIRED]);
                $count++;

                $store = $subscription->store;

                if (! $store || $store->currentSubscription()) {
                    return;
                }

                if ($store->isOwnersFirstStore()) {
                    $this->attachFreeSubscription($store);
                    if ($store->manager_id && ! $store->fresh()->allowsManagers()) {
                        $store->update(['manager_id' => null]);
                    }

                    return;
                }

                $store->update(['is_active' => false]);
            });
        }

        return $count;
    }

    private function applyPaidPeriod(
        Store $store,
        Plan $plan,
        int $durationDays,
        int $priceMmk,
        ?Payment $payment,
    ): StoreSubscription {
        $current = $store->currentSubscription();
        $endsAt = now()->addDays($durationDays);

        if ($current?->ends_at && $current->ends_at->isFuture()) {
            $endsAt = $current->ends_at->copy()->addDays($durationDays);
        }

        $this->cancelActiveSubscriptions($store);

        return StoreSubscription::query()->create([
            'store_id' => $store->id,
            'plan_id' => $plan->id,
            'payment_id' => $payment?->id,
            'status' => StoreSubscription::STATUS_ACTIVE,
            'starts_at' => now(),
            'ends_at' => $endsAt,
            'price_mmk' => $priceMmk,
        ]);
    }

    private function cancelActiveSubscriptions(Store $store): void
    {
        $store->subscriptions()
            ->where('status', StoreSubscription::STATUS_ACTIVE)
            ->update(['status' => StoreSubscription::STATUS_CANCELLED]);
    }
}
