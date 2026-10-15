<?php

namespace App\Support;

use App\Models\Plan;
use App\Models\Store;
use App\Models\StoreSubscription;
use App\Models\User;

class HeaderPlanCue
{
    public static function for(User $user): ?array
    {
        if ($user->isSuperAdmin()) {
            return null;
        }

        $stores = $user->accessibleStores()
            ->withCount('reviews')
            ->with(['subscriptions' => function ($query) {
                $query->with('plan')
                    ->where('status', StoreSubscription::STATUS_ACTIVE)
                    ->where(function ($inner) {
                        $inner->whereNull('ends_at')->orWhere('ends_at', '>', now());
                    })
                    ->latest('id');
            }])
            ->orderBy('id')
            ->get();

        if ($stores->isEmpty()) {
            return null;
        }

        $freeStore = $stores->first(fn (Store $store) => self::paidSubscription($store) === null);

        if ($freeStore) {
            return self::freeCue($user, $freeStore);
        }

        $soonest = $stores
            ->map(fn (Store $store) => [
                'store' => $store,
                'subscription' => self::paidSubscription($store),
            ])
            ->filter(fn (array $row) => $row['subscription']?->ends_at)
            ->sortBy(fn (array $row) => $row['subscription']->ends_at->getTimestamp())
            ->first();

        if (! $soonest) {
            return null;
        }

        return self::proCue($user, $soonest['store'], $soonest['subscription']);
    }

    private static function paidSubscription(Store $store): ?StoreSubscription
    {
        return $store->subscriptions->first(fn (StoreSubscription $subscription) => $subscription->plan?->isPaid());
    }

    private static function href(User $user): ?string
    {
        return $user->can('buy_plans') ? route('billing.extend') : null;
    }

    private static function freeCue(User $user, Store $store): array
    {
        $max = Plan::free()?->max_reviews ?: 30;
        $used = min((int) $store->reviews_count, $max);
        $percent = $max > 0 ? (int) round(($used / $max) * 100) : 100;

        $tone = 'default';
        if ($used >= $max) {
            $tone = 'danger';
        } elseif ($percent >= 80) {
            $tone = 'warning';
        }

        return [
            'mode' => 'free',
            'used' => $used,
            'max' => $max,
            'percent' => min(100, $percent),
            'daysLeft' => null,
            'tone' => $tone,
            'href' => self::href($user),
            'storeName' => $store->name,
            'label' => __('messages.compare_free'),
        ];
    }

    private static function proCue(User $user, Store $store, StoreSubscription $subscription): array
    {
        $daysLeft = max(0, (int) now()->diffInDays($subscription->ends_at, false));

        $tone = 'default';
        if ($daysLeft <= 3) {
            $tone = 'danger';
        } elseif ($daysLeft <= 7) {
            $tone = 'warning';
        }

        return [
            'mode' => 'pro',
            'used' => null,
            'max' => null,
            'percent' => null,
            'daysLeft' => $daysLeft,
            'tone' => $tone,
            'href' => self::href($user),
            'storeName' => $store->name,
            'label' => __('messages.compare_pro'),
        ];
    }
}
