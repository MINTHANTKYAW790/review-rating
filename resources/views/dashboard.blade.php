@extends('layouts.app')

@section('title', __('messages.dashboard'))

@section('content')
<section class="content">
<div class="container-fluid">
    <x-page-header :title="__('messages.dashboard')" />

    <div class="row">
        <div class="col-md-3 d-flex mb-3">
            <div class="card shadow-sm flex-fill stat-card">
                <div class="card-body text-center d-flex flex-column justify-content-between">
                    <div class="mb-2 text-warning stat-icon"><i class="fas fa-star"></i></div>
                    <h6 class="mb-0">{{ __('messages.top_rated_store') }}</h6>
                    @if ($store)
                        <div class="d-flex justify-content-center align-items-center flex-column mt-2 mb-1">
                            <img src="{{ $store->logo_url ? asset('storage/' . $store->logo_url) : asset('default-store.jpg') }}" class="rounded-circle mb-1" width="32" height="32" alt="Store">
                            <span>{{ $store->name }}</span>
                        </div>
                        <div class="text-warning">
                            {{ number_format($store->reviews_avg_rating_store ?? 0, 1) }} <i class="fas fa-star"></i>
                        </div>
                    @else
                        <p class="text-muted mt-2 mb-0">{{ __('messages.no_store_rated') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-3 d-flex mb-3">
            <div class="card shadow-sm flex-fill stat-card">
                <div class="card-body text-center d-flex flex-column justify-content-between">
                    <div class="mb-2 text-primary stat-icon"><i class="fas fa-comments"></i></div>
                    <h6 class="mb-0">{{ __('messages.total_reviews_received') }}</h6>
                    <h3 class="font-weight-bold mt-2">{{ $totalReviews }}</h3>
                    <span class="text-success">+{{ $reviewGrowth }}%</span>
                    <div><small class="text-muted">{{ __('messages.all_time_reviews') }}</small></div>
                </div>
            </div>
        </div>

        <div class="col-md-3 d-flex mb-3">
            <div class="card shadow-sm flex-fill stat-card">
                <div class="card-body text-center d-flex flex-column justify-content-between">
                    <div class="mb-2 text-success stat-icon"><i class="fas fa-chart-line"></i></div>
                    <h6 class="mb-0">{{ __('messages.new_reviews_this_week') }}</h6>
                    <h3 class="font-weight-bold mt-2">{{ $newReviewsThisWeek }}</h3>
                    <span class="text-success">+{{ $newReviewIncrease }}</span>
                    <div><small class="text-muted">{{ __('messages.since_last_week') }}</small></div>
                </div>
            </div>
        </div>

        <div class="col-md-3 d-flex mb-3">
            <div class="card shadow-sm flex-fill stat-card">
                <div class="card-body text-center d-flex flex-column justify-content-between">
                    <div class="mb-2 text-primary stat-icon"><i class="fas fa-crown"></i></div>
                    <h6 class="mb-0">{{ __('messages.top_rated_staff') }}</h6>
                    @if ($topStaff)
                        <div class="d-flex justify-content-center align-items-center flex-column mt-2 mb-1">
                            <img src="{{ $topStaff->image_url ? asset('storage/' . $topStaff->image_url) : asset('default-user.jpg') }}" class="rounded-circle mb-1" width="32" height="32" alt="Staff">
                            <span>{{ $topStaff->name }}</span>
                            <span class="small text-muted">{{ __('messages.from_store') }} {{ $topStaff->store->name }}</span>
                        </div>
                        <div class="text-warning">
                            {{ number_format($topStaff->reviews_avg_rating_staff ?? 0, 1) }} <i class="fas fa-star"></i>
                        </div>
                    @else
                        <p class="text-muted mt-2 mb-0">{{ __('messages.no_staff_rated') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if ($hasStores)
        <div class="row">
            @foreach ($ratingCharts as $chart)
                <div class="col-md-6 mb-3">
                    <div class="card shadow-sm rating-chart-card">
                        <div class="card-body">
                            <h6 class="mb-1">{{ __('messages.ratings_by_star') }}</h6>
                            <p class="text-muted small mb-3">{{ $chart['storeName'] }}</p>
                            @if ($chart['total'] > 0)
                                <canvas
                                    class="js-rating-star-chart"
                                    data-counts='@json($chart['counts'])'
                                    data-labels='@json($starLabels)'
                                ></canvas>
                            @else
                                <p class="text-muted mb-0">{{ __('messages.no_reviews_for_chart') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <x-form-card :title="__('messages.quick_actions')">
            <div class="row">
                @if ($store && auth()->user()->can('update', $store))
                <div class="col-md-3 col-sm-6 mb-2">
                    <a href="{{ route('store.edit', $store->id) }}" class="btn btn-primary btn-block">
                        <i class="fas fa-edit mr-1"></i> {{ __('messages.edit_store_profile') }}
                    </a>
                </div>
                @endif
                @can('create', \App\Models\Staff::class)
                <div class="col-md-3 col-sm-6 mb-2">
                    <a href="{{ route('staff.index') }}" class="btn btn-outline-primary btn-block">
                        <i class="fas fa-user-plus mr-1"></i> {{ __('messages.add_new_staff') }}
                    </a>
                </div>
                @endcan
                @if ($store)
                <div class="col-md-3 col-sm-6 mb-2">
                    <a href="{{ route('qr.download', $store->id) }}" class="btn btn-outline-secondary btn-block">
                        <i class="fas fa-qrcode mr-1"></i> {{ __('messages.generate_qr_code') }}
                    </a>
                </div>
                @endif
                @can('view_reviews')
                <div class="col-md-3 col-sm-6 mb-2">
                    <a href="{{ route('review.index') }}" class="btn btn-outline-primary btn-block">
                        <i class="fas fa-eye mr-1"></i> {{ __('messages.view_all_reviews') }}
                    </a>
                </div>
                @endcan
            </div>
        </x-form-card>
    @else
        <x-form-card>
            <x-empty-state :message="$isManager ? __('messages.welcome_no_managed_stores') : __('messages.welcome_no_stores')" icon="fa-store">
                <x-slot:actions>
                    @if ($isManager)
                        <p class="text-muted mb-0">{{ __('messages.welcome_no_managed_stores_sub') }}</p>
                    @else
                        <p class="text-muted mb-3">{{ __('messages.welcome_no_stores_sub') }}</p>
                        @can('create', \App\Models\Store::class)
                        <a href="{{ route('store.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle mr-1"></i> {{ __('messages.create_first_store') }}
                        </a>
                        @endcan
                    @endif
                </x-slot:actions>
            </x-empty-state>
        </x-form-card>
    @endif
</div>
</section>
@endsection
