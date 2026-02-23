@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4 d-flex">
        <!-- Overall Store Rating -->
        <div class="col-md-3 d-flex">
            <div class="card shadow-sm border-0 flex-fill" style="min-height: 180px;">
                <div class="card-body text-center d-flex flex-column justify-content-between h-100">
                    <div class="mb-2 text-warning fs-4">
                        <i class="fas fa-star"></i>
                    </div>
                    <h6 class="mb-0">{{ __('messages.top_rated_store') }}</h6>
                    @if ($store)
                        <div class="d-flex justify-content-center align-items-center flex-column mt-2 mb-1">
                            <img src="{{ $store->logo_url ? asset('storage/' . $store->logo_url) : asset('default-store.jpg') }}" class="rounded-circle me-2" width="32" height="32" alt="Staff">
                            <span>{{ $store->name }}</span>
                        </div>
                        <div class="text-warning">
                            {{ number_format($store->reviews_avg_rating_store ?? 0, 1) }} <i class="fas fa-star"></i>
                        </div>
                    @else
                        <p class="text-muted mt-2">{{ __('messages.no_store_rated') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Total Reviews Received -->
            <div class="col-md-3 d-flex">
                <div class="card shadow-sm border-0 flex-fill" style="min-height: 180px;">
                    <div class="card-body text-center d-flex flex-column justify-content-between h-100">
                    <div class="mb-2 text-primary fs-4">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h6 class="mb-0">{{ __('messages.total_reviews_received') }}</h6>
                    <h3 class="fw-bold mt-2">{{ $totalReviews }}</h3>
                    <span class="text-success">+{{ $reviewGrowth }}%</span>
                    <div><small>{{ __('messages.all_time_reviews') }}</small></div>
                </div>
            </div>
        </div>

        <!-- New Reviews This Week -->
            <div class="col-md-3 d-flex">
                <div class="card shadow-sm border-0 flex-fill" style="min-height: 180px;">
                    <div class="card-body text-center d-flex flex-column justify-content-between h-100">
                    <div class="mb-2 text-success fs-4">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h6 class="mb-0">{{ __('messages.new_reviews_this_week') }}</h6>
                    <h3 class="fw-bold mt-2">{{ $newReviewsThisWeek }}</h3>
                    <span class="text-success">+{{ $newReviewIncrease }}</span>
                    <div><small>{{ __('messages.since_last_week') }}</small></div>
                </div>
            </div>
        </div>

        <!-- Top Rated Staff Member -->
            <div class="col-md-3 d-flex">
                <div class="card shadow-sm border-0 flex-fill" style="min-height: 180px;">
                    <div class="card-body text-center d-flex flex-column justify-content-between h-100">
                    <div class="mb-2 text-purple fs-4">
                        <i class="fas fa-crown"></i>
                    </div>
                    <h6 class="mb-0">{{ __('messages.top_rated_staff') }}</h6>
                    @if ($topStaff)
                        <div class="d-flex justify-content-center align-items-center flex-column mt-2 mb-1">
                            <img src="{{ $topStaff->image_url ? asset('storage/' . $topStaff->image_url) : asset('default-user.jpg') }}" class="rounded-circle me-2" width="32" height="32" alt="Staff">
                            <span>{{ $topStaff->name }}</span>
                            <span class="small text-muted">{{ __('messages.from_store') }} {{ $topStaff->store->name }}</span>
                        </div>
                        <div class="text-warning">
                            {{ number_format($topStaff->reviews_avg_rating_staff ?? 0, 1) }} <i class="fas fa-star"></i>
                        </div>
                    @else
                        <p class="text-muted mt-2">{{ __('messages.no_staff_rated') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    @if ($hasStores)
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="mb-4">{{ __('messages.quick_actions') }}</h5>
                <div class="row g-3">
                    <div class="col-md-3 col-sm-6">
                        <a href="{{ route('store.edit', $store->id) }}" class="btn btn-primary btn-lg w-100 d-flex align-items-center justify-content-center">
                            <i class="fas fa-edit me-2"></i> {{ __('messages.edit_store_profile') }}
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="{{ route('staff.create') }}" class="btn btn-success btn-lg w-100 d-flex align-items-center justify-content-center">
                            <i class="fas fa-user-plus me-2"></i> {{ __('messages.add_new_staff') }}
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="{{ route('qr.download', $store->id) }}" class="btn btn-warning btn-lg w-100 d-flex align-items-center justify-content-center text-white">
                            <i class="fas fa-qrcode me-2"></i> {{ __('messages.generate_qr_code') }}
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="{{ route('review.index') }}" class="btn btn-lg w-100 d-flex align-items-center justify-content-center" style="background-color: #8e44ad; color: white;">
                            <i class="fas fa-eye me-2"></i> {{ __('messages.view_all_reviews') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card shadow-sm border-0">
            <div class="card-body text-center">
                <h5 class="mb-3">{{ __('messages.welcome_no_stores') }}</h5>
                <p class="text-muted">{{ __('messages.welcome_no_stores_sub') }}</p>
                <a href="{{ route('store.create') }}" class="btn btn-primary btn-lg mt-3">
                    <i class="fas fa-plus-circle me-2"></i> {{ __('messages.create_first_store') }}
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
