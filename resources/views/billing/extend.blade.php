@extends('layouts.app')

@section('title', __('messages.extend_stores_card'))

@section('content')
<section class="content">
    <div class="container-fluid">
        <x-page-header :title="__('messages.extend_stores_card')" :subtitle="__('messages.extend_stores_subtitle')" />

        @include('billing.partials.pending-alert')

        @if ($plans->isEmpty() || ! $plan)
            <x-form-card>
                <p class="text-muted mb-0">{{ __('messages.plan_unavailable') }}</p>
            </x-form-card>
        @elseif ($stores->isEmpty())
            <x-form-card>
                <p class="text-muted mb-0">{{ __('messages.no_stores_yet_extend') }}</p>
            </x-form-card>
        @else
            <x-form-card :title="__('messages.keep_pro_heading')" class="mb-3">
                @include('billing.partials.plan-compare', ['proShopValue' => __('messages.compare_pro_shop_keep')])
            </x-form-card>

            <div class="row">
                <div class="col-lg-7 mb-3">
                    <x-form-card :title="__('messages.extend_stores_card')">
                        <form method="POST" action="{{ route('billing.store') }}" enctype="multipart/form-data" id="extend-form">
                            @csrf
                            <input type="hidden" name="intent" value="extend">

                            @foreach ($stores as $store)
                                @php $subscription = $store->current_subscription; @endphp
                                <div class="store-select-row">
                                    <div class="custom-control custom-checkbox mb-0">
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
                                </div>
                            @endforeach

                            <p class="font-weight-bold mt-3 mb-2">{{ __('messages.choose_duration') }}</p>
                            <div class="row">
                                @foreach ($plans as $paidPlan)
                                    @php
                                        $nominalDays = match ($paidPlan->slug) {
                                            \App\Models\Plan::SLUG_PRO => 30,
                                            \App\Models\Plan::SLUG_PRO_3M => 90,
                                            \App\Models\Plan::SLUG_PRO_6M => 180,
                                            \App\Models\Plan::SLUG_PRO_9M => 270,
                                            \App\Models\Plan::SLUG_PRO_1Y => 360,
                                            default => $paidPlan->duration_days,
                                        };
                                        $extraDays = $paidPlan->duration_days - $nominalDays;
                                    @endphp
                                    <div class="col-md-6 col-lg-4 mb-2">
                                        <div class="duration-option">
                                            <input type="radio" class="duration-radio" name="plan_id" id="plan-{{ $paidPlan->id }}" value="{{ $paidPlan->id }}" data-price="{{ $paidPlan->price_mmk }}" @checked(old('plan_id', $plan->id) == $paidPlan->id) {{ $pendingPayment ? 'disabled' : '' }}>
                                            <label class="duration-option-card" for="plan-{{ $paidPlan->id }}">
                                                <span class="duration-option-name">{{ $paidPlan->name }}</span>
                                                <span class="text-muted small d-block">
                                                    {{ __('messages.duration_price_line', ['days' => $paidPlan->duration_days, 'price' => number_format($paidPlan->price_mmk)]) }}
                                                </span>
                                                @if ($extraDays > 0)
                                                    <span class="badge badge-success mt-1">{{ __('messages.extra_days_badge', ['days' => $extraDays]) }}</span>
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="invoice-total-bar">
                                <span class="text-muted" id="invoice-summary">{{ trans_choice('messages.invoice_for_shops', $stores->count(), ['count' => $stores->count(), 'price' => number_format($plan->price_mmk)]) }}</span>
                                <strong id="invoice-total">{{ number_format($plan->price_mmk * $stores->count()) }} MMK</strong>
                            </div>

                            @include('billing.partials.payment-fields', ['prefix' => 'extend', 'disabled' => (bool) $pendingPayment])
                            <button type="submit" class="btn btn-primary" {{ $pendingPayment ? 'disabled' : '' }}>{{ __('messages.submit_payment') }}</button>
                        </form>
                    </x-form-card>
                </div>
                <div class="col-lg-5 mb-3">
                    @include('billing.partials.transfer-details')
                </div>
            </div>
        @endif
    </div>
</section>

@if (! $pendingPayment && $stores->isNotEmpty() && $plans->isNotEmpty())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const totalEl = document.getElementById('invoice-total');
        const summaryEl = document.getElementById('invoice-summary');

        function selectedPrice() {
            const radio = document.querySelector('.duration-radio:checked');
            return radio ? parseInt(radio.dataset.price || '0', 10) : 0;
        }

        function refreshTotal() {
            const stores = document.querySelectorAll('.store-check:checked').length;
            const price = selectedPrice();
            totalEl.textContent = new Intl.NumberFormat().format(price * stores) + ' MMK';
            summaryEl.textContent = stores + ' × ' + new Intl.NumberFormat().format(price) + ' MMK';
        }

        document.querySelectorAll('.store-check, .duration-radio').forEach(function (el) {
            el.addEventListener('change', refreshTotal);
        });
        refreshTotal();
    });
</script>
@endif
@endsection
