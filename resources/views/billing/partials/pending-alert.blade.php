@if ($pendingPayment)
    <div class="alert alert-warning">
        {{ __('messages.pending_payment_wait') }}
        <a href="{{ route('billing.show', $pendingPayment) }}">{{ __('messages.view_payment') }}</a>
    </div>
@endif
