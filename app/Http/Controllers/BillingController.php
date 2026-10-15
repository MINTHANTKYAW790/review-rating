<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentSubmitRequest;
use App\Models\BillingSetting;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\User;
use App\Services\BillingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BillingController extends Controller
{
    public function __construct(private readonly BillingService $billing)
    {
        $this->middleware('permission:buy_plans');
    }

    public function index(): RedirectResponse
    {
        return redirect()->route('billing.add-store');
    }

    public function addStore(): View
    {
        $user = auth('web')->user();

        return view('billing.add-store', [
            'plan' => Plan::pro(),
            'settings' => BillingSetting::current(),
            'pendingPayment' => $this->pendingPayment($user),
            'unusedCredits' => $user->unusedStoreCredits()->count(),
        ]);
    }

    public function extend(): View
    {
        $user = auth('web')->user();
        $plans = Plan::paid();
        $plan = Plan::pro();
        $stores = $user->stores()
            ->withCount(['staff', 'reviews'])
            ->with(['subscriptions' => fn ($query) => $query->latest('id')])
            ->orderBy('name')
            ->get();

        $stores->each(function ($store) {
            $store->setRelation('current_subscription', $store->currentSubscription());
        });

        return view('billing.extend', [
            'stores' => $stores,
            'plans' => $plans,
            'plan' => $plan,
            'settings' => BillingSetting::current(),
            'pendingPayment' => $this->pendingPayment($user),
        ]);
    }

    public function payments(): View
    {
        $user = auth('web')->user();
        $payments = $user->payments()
            ->with('items.store')
            ->latest()
            ->paginate(20);

        return view('billing.payments', [
            'payments' => $payments,
            'pendingPayment' => $this->pendingPayment($user),
        ]);
    }

    public function store(PaymentSubmitRequest $request): RedirectResponse
    {
        $payment = $this->billing->submitInvoice(
            $request->user(),
            $request->string('intent')->toString(),
            $request->resolvedPlan(),
            $request->input('store_ids', []),
            $request->string('method')->toString(),
            $request->string('reference')->toString(),
            $request->file('screenshot'),
            $request->input('owner_note'),
        );

        return redirect()->route('billing.show', $payment)->with('success', 'Payment submitted. We will review it shortly.');
    }

    public function show(Payment $payment): View
    {
        $this->authorize('view', $payment);

        $payment->load(['items.store', 'items.plan']);

        return view('billing.show', compact('payment'));
    }

    private function pendingPayment(User $user): ?Payment
    {
        return $user->payments()->where('status', Payment::STATUS_PENDING)->latest()->first();
    }
}
