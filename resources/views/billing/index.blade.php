@extends('layouts.app')

@section('title', __('messages.buy_plans'))

@section('content')
<section class="content">
    <div class="container-fluid">
        <x-page-header :title="__('messages.buy_plans')" :subtitle="__('messages.buy_plans_subtitle')" />

        @if ($unusedCredits > 0)
            <div class="alert alert-info">
                {{ trans_choice('messages.store_credits_available', $unusedCredits, ['count' => $unusedCredits]) }}
                <a href="{{ route('store.create') }}">{{ __('messages.create_store') }}</a>
            </div>
        @endif

        @if ($pendingPayment)
            <div class="alert alert-warning">
                {{ __('messages.pending_payment_wait') }}
                <a href="{{ route('billing.show', $pendingPayment) }}">{{ __('messages.view_payment') }}</a>
            </div>
        @endif

        @if ($plans->isEmpty() || ! $plan)
            <x-form-card>
                <p class="text-muted mb-0">{{ __('messages.plan_unavailable') }}</p>
            </x-form-card>
        @else
            <div class="row">
                <div class="col-lg-6 mb-3">
                    <x-form-card :title="__('messages.add_one_store_card')">
                        <p class="text-muted">{{ __('messages.add_one_store_description') }}</p>
                        <ul class="mb-3">
                            <li>{{ __('messages.plan_feature_staff') }}</li>
                            <li>{{ __('messages.plan_feature_manager') }}</li>
                            <li>{{ __('messages.plan_feature_reviews') }}</li>
                            <li>{{ __('messages.plan_feature_month', ['days' => $plan->duration_days]) }}</li>
                        </ul>
                        <p class="h5">{{ number_format($plan->price_mmk) }} MMK</p>

                        <form method="POST" action="{{ route('billing.store') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="intent" value="add_store">
                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                            @include('billing.partials.payment-fields', ['prefix' => 'add', 'disabled' => (bool) $pendingPayment])
                            <button type="submit" class="btn btn-primary" {{ $pendingPayment ? 'disabled' : '' }}>{{ __('messages.submit_payment') }}</button>
                        </form>
                    </x-form-card>
                </div>

                <div class="col-lg-6 mb-3">
                    <x-form-card :title="__('messages.extend_stores_card')">
                        @if ($stores->isEmpty())
                            <p class="text-muted mb-0">{{ __('messages.no_stores_yet_extend') }}</p>
                        @else
                            <form method="POST" action="{{ route('billing.store') }}" enctype="multipart/form-data" id="extend-form">
                                @csrf
                                <input type="hidden" name="intent" value="extend">

                                @foreach ($stores as $store)
                                    @php $subscription = $store->current_subscription; @endphp
                                    <div class="custom-control custom-checkbox mb-2">
                                        <input type="checkbox" class="custom-control-input store-check" name="store_ids[]" id="store-{{ $store->id }}" value="{{ $store->id }}" @checked(in_array($store->id, old('store_ids', $stores->pluck('id')->all()))) {{ $pendingPayment ? 'disabled' : '' }}>
                                        <label class="custom-control-label" for="store-{{ $store->id }}">
                                            <strong>{{ $store->name }}</strong>
                                            <span class="text-muted small d-block">
                                                {{ $subscription?->plan?->name ?? __('messages.no_plan') }}
                                                · {{ __('messages.staff_count_label') }}: {{ $store->staff_count }} / {{ $subscription?->plan?->max_staff ?? '—' }}
                                                · {{ __('messages.review_count_label') }}: {{ $store->reviews_count }}{{ $subscription?->plan?->max_reviews ? ' / '.$subscription->plan->max_reviews : '' }}
                                                @if ($subscription?->ends_at)
                                                    · {{ __('messages.ends_on', ['date' => $subscription->ends_at->format('Y-m-d')]) }}
                                                @endif
                                            </span>
                                        </label>
                                    </div>
                                @endforeach

                                <p class="font-weight-bold mt-3 mb-2">{{ __('messages.choose_duration') }}</p>
                                @foreach ($plans as $paidPlan)
                                    <div class="custom-control custom-radio mb-2">
                                        <input type="radio" class="custom-control-input duration-radio" name="plan_id" id="plan-{{ $paidPlan->id }}" value="{{ $paidPlan->id }}" data-price="{{ $paidPlan->price_mmk }}" @checked(old('plan_id', $plan->id) == $paidPlan->id) {{ $pendingPayment ? 'disabled' : '' }}>
                                        <label class="custom-control-label" for="plan-{{ $paidPlan->id }}">
                                            {{ $paidPlan->name }}
                                            <span class="text-muted small d-block">
                                                {{ __('messages.duration_price_line', ['days' => $paidPlan->duration_days, 'price' => number_format($paidPlan->price_mmk)]) }}
                                            </span>
                                        </label>
                                    </div>
                                @endforeach

                                <p class="h5" id="invoice-total">{{ number_format($plan->price_mmk * $stores->count()) }} MMK</p>

                                @include('billing.partials.payment-fields', ['prefix' => 'extend', 'disabled' => (bool) $pendingPayment])
                                <button type="submit" class="btn btn-primary" {{ $pendingPayment ? 'disabled' : '' }}>{{ __('messages.submit_payment') }}</button>
                            </form>
                        @endif
                    </x-form-card>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-6 mb-3">
                <x-form-card :title="__('messages.transfer_to')">
                    <p class="mb-1"><strong>{{ $settings->account_name }}</strong></p>
                    <p class="mb-1">KBZ Pay: {{ $settings->kbz_number ?: '—' }}</p>
                    <p class="mb-1">Wave Money: {{ $settings->wave_number ?: '—' }}</p>
                    @if ($settings->instructions)
                        <p class="mb-0 text-muted">{{ $settings->instructions }}</p>
                    @endif
                </x-form-card>
            </div>
            <div class="col-lg-6 mb-3">
                <x-form-card :title="__('messages.recent_payments')">
                    @forelse ($payments as $payment)
                        <p class="mb-2">
                            <a href="{{ route('billing.show', $payment) }}">#{{ $payment->id }}</a>
                            · {{ number_format($payment->amount_mmk) }} MMK
                            · {{ ucfirst($payment->status) }}
                        </p>
                    @empty
                        <p class="text-muted mb-0">{{ __('messages.no_payments_yet') }}</p>
                    @endforelse
                </x-form-card>
            </div>
        </div>
    </div>
</section>

@if ($plans->isNotEmpty() && ! $pendingPayment && $stores->isNotEmpty())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const totalEl = document.getElementById('invoice-total');

        function selectedPrice() {
            const radio = document.querySelector('.duration-radio:checked');
            return radio ? parseInt(radio.dataset.price || '0', 10) : 0;
        }

        function refreshTotal() {
            const stores = document.querySelectorAll('.store-check:checked').length;
            totalEl.textContent = new Intl.NumberFormat().format(selectedPrice() * stores) + ' MMK';
        }

        document.querySelectorAll('.store-check, .duration-radio').forEach(function (el) {
            el.addEventListener('change', refreshTotal);
        });
        refreshTotal();
    });
</script>
@endif
@endsection
