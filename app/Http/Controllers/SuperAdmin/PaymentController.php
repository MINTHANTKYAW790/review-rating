<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentReviewRequest;
use App\Models\Payment;
use App\Services\BillingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(private readonly BillingService $billing) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Payment::class);

        $status = $request->string('status')->toString();

        $payments = Payment::query()
            ->with(['user', 'items.store'])
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('superadmin.payments.index', compact('payments', 'status'));
    }

    public function show(Payment $payment): View
    {
        $this->authorize('view', $payment);

        $payment->load(['user', 'items.store', 'items.plan', 'reviewer', 'credits']);

        return view('superadmin.payments.show', compact('payment'));
    }

    public function approve(PaymentReviewRequest $request, Payment $payment): RedirectResponse
    {
        $this->authorize('review', $payment);

        $this->billing->approve($payment, $request->user(), $request->input('admin_note'));

        return redirect()->route('superadmin.payments.show', $payment)->with('success', 'Payment approved. Subscriptions and store credits were applied.');
    }

    public function reject(PaymentReviewRequest $request, Payment $payment): RedirectResponse
    {
        $this->authorize('review', $payment);

        $this->billing->reject($payment, $request->user(), $request->input('admin_note'));

        return redirect()->route('superadmin.payments.show', $payment)->with('success', 'Payment rejected.');
    }
}
