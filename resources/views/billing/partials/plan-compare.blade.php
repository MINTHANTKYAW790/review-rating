@php
    $proShopValue = $proShopValue ?? __('messages.compare_pro_shop');
    $rows = [
        [__('messages.staff_count_label'), __('messages.compare_free_staff'), __('messages.compare_pro_staff')],
        [__('messages.store_manager'), __('messages.compare_free_manager'), __('messages.compare_pro_manager')],
        [__('messages.review_count_label'), __('messages.compare_free_reviews'), __('messages.compare_pro_reviews')],
        [__('messages.compare_extra_shop'), __('messages.compare_free_shop'), $proShopValue],
    ];
@endphp

<div class="plan-compare-table mb-3">
    <div class="plan-compare-head">
        <div class="plan-compare-feature-head"></div>
        <div class="plan-compare-free-head">{{ __('messages.compare_free') }}</div>
        <div class="plan-compare-pro-head">
            {{ __('messages.compare_pro') }}
            <span class="badge badge-primary ml-1">{{ __('messages.compare_recommended') }}</span>
        </div>
    </div>
    @foreach ($rows as [$feature, $freeValue, $proValue])
        <div class="plan-compare-row">
            <div class="plan-compare-feature">{{ $feature }}</div>
            <div class="plan-compare-free">
                <i class="fas fa-times" aria-hidden="true"></i>
                <span>{{ $freeValue }}</span>
            </div>
            <div class="plan-compare-pro">
                <i class="fas fa-check" aria-hidden="true"></i>
                <span>{{ $proValue }}</span>
            </div>
        </div>
    @endforeach
</div>
