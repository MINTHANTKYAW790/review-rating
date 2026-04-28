@extends('layouts.app')

@section('content')
<div
    class="container mt-4"
    id="review-page"
    x-data="{ showDeleteModal: false, deletingReviewId: null }"
    data-unread-count="{{ $unreadCount }}"
    data-mark-read-url-template="{{ route('review.mark-read', ['review' => '__REVIEW__']) }}"
    data-review-channel="{{ $reviewChannel }}"
>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Reviews & Ratings</h4>
        <span class="badge bg-danger fs-6" id="review-unread-badge">
            Unread: <span id="review-unread-count">{{ $unreadCount }}</span>
        </span>
    </div>

    @foreach ($reviews as $review)
    <div
        class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden js-review-card {{ is_null($review->read_at) ? 'border border-warning-subtle' : '' }}"
        data-review-id="{{ $review->id }}"
        data-read-at="{{ optional($review->read_at)->toISOString() }}"
    >
        <div class="card-header bg-white border-bottom border-light py-3 px-4">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted small fw-medium">
                    <i class="far fa-calendar-alt me-2 text-primary"></i>{{ $review->created_at->format('F d, Y') }}
                </span>
                <h5 class="text-uppercase fw-bold mb-0 small text-black tracking-tight">{{ $review->store->name ?: 'No feedback provided' }}</h5>
                <!-- <button class="btn btn-link btn-sm text-danger text-decoration-none p-0"
                    x-on:click="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'confirm-review-deletion' })); deletingReviewId = {{ $review->id }}">
                    <i class="fas fa-trash-alt small"></i>
                </button> -->
            </div>
        </div>

        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="p-3 rounded-3 bg-light border border-light h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-uppercase fw-bold mb-0 small text-secondary tracking-tight">Store Experience</h6>
                            <div class="bg-white px-2 py-1 rounded-2 shadow-sm border">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $review->rating_store ? 'text-warning' : 'text-light' }} x-small"></i>
                                    @endfor
                                    <span class="ms-1 fw-bold small">{{ number_format($review->rating_store, 1) }}</span>
                            </div>
                        </div>
                        <p class="mb-0 text-dark fw-medium" style="font-size: 0.95rem;">
                            {{ $review->comment ?: 'No feedback provided' }}
                        </p>
                        <!-- @if ($review->comment)
                        <form action="{{ route('review.deleteComment', $review->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this comment?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link btn-sm text-danger text-decoration-none p-0">
                                <i class="fas fa-trash-alt small me-1"></i> Delete Comment
                            </button>
                        </form> -->
                        <!-- @endif -->
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="p-3 rounded-3 bg-light border border-light h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-uppercase fw-bold mb-0 small text-secondary tracking-tight">Staff Service</h6>
                            <div class="bg-white px-2 py-1 rounded-2 shadow-sm border">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $review->rating_staff ? 'text-warning' : 'text-light' }} x-small"></i>
                                    @endfor
                                    <span class="ms-1 fw-bold small">{{ number_format($review->rating_staff, 1) }}</span>
                            </div>
                        </div>
                        <p class="mb-2 text-dark fw-medium" style="font-size: 0.95rem;">
                            {{ $review->comment_staff ?: 'No feedback provided' }}
                        </p>

                        @if ($review->staff && $review->staff->name)
                        <div class="mt-2 pt-2 border-top border-light-subtle">
                            <span class="badge bg-white text-primary border border-primary-subtle fw-normal">
                                <i class="fas fa-user-sparkles me-1"></i> {{ $review->staff->name }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Pagination Links -->
    <div class="d-flex justify-content-center mt-4">
        {{ $reviews->links('pagination::bootstrap-4') }}
    </div>

    <!-- Delete Review Confirmation Modal -->
    <x-modal name="confirm-review-deletion" focusable>
        <form method="post" x-bind:action="'{{ route('review.destroy', '') }}/' + deletingReviewId" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900">
                Are you sure you want to delete this review?
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Once this review is deleted, all of its resources and data will be permanently deleted.
            </p>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'confirm-review-deletion' }))">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('Delete Review') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</div>
@endsection