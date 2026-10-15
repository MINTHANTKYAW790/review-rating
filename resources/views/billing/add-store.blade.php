@extends('layouts.app')

@section('title', __('messages.add_one_store_card'))

@section('content')
<section class="content">
    <div class="container-fluid">
        <x-page-header :title="__('messages.add_one_store_card')" :subtitle="__('messages.add_one_store_description')" />

        @if ($unusedCredits > 0)
            <div class="alert alert-info">
                {{ trans_choice('messages.store_credits_available', $unusedCredits, ['count' => $unusedCredits]) }}
                <a href="{{ route('store.create') }}">{{ __('messages.create_store') }}</a>
            </div>
        @endif

        @include('billing.partials.pending-alert')

        @if (! $plan)
            <x-form-card>
                <p class="text-muted mb-0">{{ __('messages.plan_unavailable') }}</p>
            </x-form-card>
        @else
            <div class="row">
                <div class="col-lg-6 mb-3">
                    <x-form-card :title="__('messages.compare_pro')">
                        <p class="text-muted">{{ __('messages.add_one_store_description') }}</p>
                        @include('billing.partials.plan-compare')
                        <p class="plan-price mb-1">{{ number_format($plan->price_mmk) }} MMK</p>
                        <p class="text-muted small mb-0">{{ __('messages.plan_feature_month', ['days' => $plan->duration_days]) }}</p>
                    </x-form-card>
                </div>
                <div class="col-lg-6 mb-3">
                    <x-form-card :title="__('messages.submit_payment')">
                        <form method="POST" action="{{ route('billing.store') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="intent" value="add_store">
                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                            @include('billing.partials.payment-fields', ['prefix' => 'add', 'disabled' => (bool) $pendingPayment])
                            <button type="submit" class="btn btn-primary" {{ $pendingPayment ? 'disabled' : '' }}>{{ __('messages.submit_payment') }}</button>
                        </form>
                    </x-form-card>
                    <div class="mt-3">
                        @include('billing.partials.transfer-details')
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
