@extends('layouts.app')

@section('content')
<div class="container mt-4">

    @foreach ($reviews as $review)
        <div class="card border-0 shadow-sm mb-4 rounded-4">
            <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-start">
                <!-- Left: User Info and Comment -->
                <div class="d-flex flex-column flex-md-row">
                    <img src="{{ asset($review->user_image ?? 'default-user.png') }}" alt="{{ $review->user_name ?? 'Guest' }}" class="rounded-circle me-3" width="60" height="60" style="object-fit: cover;">
                    <div>
                        <h5 class="fw-bold mb-0">{{ $review->user_name ?? 'Guest User' }}</h5>
                        <small class="text-muted">{{ $review->created_at->format('F d, Y') }}</small>
                        <p class="mt-2 mb-2 text-body">{{ $review->comment }}</p>

                        @if ($review->staff && $review->staff->name)
                            <p class="mb-1 text-muted">
                                <strong>Associated with:</strong>
                                <span class="badge bg-primary-subtle text-primary">{{ $review->staff->name }}</span>
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Right: Rating and Actions -->
                <div class="ms-md-4 mt-3 mt-md-0 d-flex flex-column align-items-end">
                    <div class="mb-2">
                        <span>Store rating - </span>
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= $review->rating_store ? 'text-warning' : 'text-secondary' }}"></i>
                        @endfor
                        <span class="ms-1 fw-semibold">{{ number_format($review->rating_store, 1) }}</span>
                    </div>

                    <div class="mb-2">
                        <span>Staff rating - </span>
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= $review->rating_store ? 'text-warning' : 'text-secondary' }}"></i>
                        @endfor
                        <span class="ms-1 fw-semibold">{{ number_format($review->rating_staff, 1) }}</span>
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-success">
                            <i class="fas fa-check me-1"></i> Mark as Read
                        </button>
                        <button class="btn btn-sm btn-outline-warning">
                            <i class="fas fa-flag me-1"></i> Report Spam
                        </button>
                        <button class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-trash me-1"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Pagination Links -->
    <div class="d-flex justify-content-center mt-4">
        {{ $reviews->links() }}
    </div>

</div>
@endsection
