@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <!-- Overall Store Rating -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="mb-2 text-warning fs-4">
                        <i class="fas fa-star"></i>
                    </div>
                    <h6 class="mb-0">Overall Store Rating</h6>
                    <h3 class="fw-bold mt-2">{{ $store->average_rating ?? '0.0' }}</h3>
                    <div class="text-warning mb-2">
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star{{ $i <= floor($store->average_rating) ? '' : '-o' }}"></i>
                        @endfor
                    </div>
                    <small>Out of 5 stars</small>
                </div>
            </div>
        </div>

        <!-- Total Reviews Received -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="mb-2 text-primary fs-4">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h6 class="mb-0">Total Reviews Received</h6>
                    <h3 class="fw-bold mt-2">{{ $totalReviews }}</h3>
                    <span class="text-success">+{{ $reviewGrowth }}%</span>
                    <div><small>All time reviews</small></div>
                </div>
            </div>
        </div>

        <!-- New Reviews This Week -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="mb-2 text-success fs-4">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h6 class="mb-0">New Reviews This Week</h6>
                    <h3 class="fw-bold mt-2">{{ $newReviewsThisWeek }}</h3>
                    <span class="text-success">+{{ $newReviewIncrease }}</span>
                    <div><small>Since last week</small></div>
                </div>
            </div>
        </div>

        <!-- Top Rated Staff Member -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="mb-2 text-purple fs-4">
                        <i class="fas fa-crown"></i>
                    </div>
                    <h6 class="mb-0">Top Rated Staff Member</h6>
                    @if ($topStaff)
                        <div class="d-flex justify-content-center align-items-center mt-2 mb-1">
                            <img src="{{ $topStaff->image_url }}" class="rounded-circle me-2" width="32" height="32" alt="Staff">
                            <span>{{ $topStaff->name }}</span>
                        </div>
                        <div class="text-warning">
                            {{ $topStaff->rating }} <i class="fas fa-star"></i>
                        </div>
                    @else
                        <p class="text-muted mt-2">No staff rated</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="mb-3">Quick Actions</h5>
            <div class="d-flex flex-wrap gap-3">
                <a href="{{ route('store.edit', $store->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> Edit Store Profile
                </a>
                <a href="{{ route('staff.create', $store->id) }}" class="btn btn-success">
                    <i class="fas fa-user-plus me-1"></i> Add New Staff
                </a>
                <a href="{{ route('public.qr-preview', $store->id) }}" class="btn btn-warning text-white">
                    <i class="fas fa-qrcode me-1"></i> Generate QR Code
                </a>
                <a href="{{ route('review.index', $store->id) }}" class="btn btn-purple text-white" style="background-color: #8e44ad;">
                    <i class="fas fa-eye me-1"></i> View All Reviews
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
