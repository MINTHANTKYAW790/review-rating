@extends('layouts.app')

@section('title', __('messages.reviews_ratings'))

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <div class="h4 mb-0 font-weight-bold text-dark">
                Reviews <span class="text-muted">&gt;</span> <span class="text-dark">Review Details</span>
            </div>

            <div class="d-flex align-items-center flex-wrap">
                @can('update', $review)
                    <form method="POST" action="{{ route('review.star', $review) }}" class="mr-2 mb-2">
                        @csrf
                        <button type="submit" class="btn {{ $review->is_important ? 'btn-warning' : 'btn-outline-secondary' }}">
                            <i class="{{ $review->is_important ? 'fas' : 'far' }} fa-star mr-1"></i>
                            {{ $review->is_important ? __('messages.review_unstar') : __('messages.review_star') }}
                        </button>
                    </form>
                    @if ($review->isDone())
                        <form method="POST" action="{{ route('review.reopen', $review) }}" class="mr-2 mb-2">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary">{{ __('messages.review_reopen') }}</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('review.done', $review) }}" class="mr-2 mb-2">
                            @csrf
                            <button type="submit" class="btn btn-primary">{{ __('messages.review_mark_done') }}</button>
                        </form>
                    @endif
                @endcan
                <a href="{{ route('review.index') }}" class="btn btn-outline-secondary mb-2">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Reviews
                </a>
            </div>
        </div>

        <div class="card card-primary card-outline shadow-sm border-0 mb-4">
            <div class="card-header">
                <h3 class="card-title mb-0">
                    <i class="fas fa-comment-dots mr-2"></i>Review Information
                </h3>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="small text-uppercase text-muted font-weight-bold d-block mb-2">Shop</label>
                        <div class="h5 mb-0 text-dark">{{ $review->store?->name ?? '-' }}</div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="small text-uppercase text-muted font-weight-bold d-block mb-2">Staff</label>
                        <div class="h5 mb-0 text-dark">{{ $review->staff?->name ?? '-' }}</div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="small text-uppercase text-muted font-weight-bold d-block mb-2">Shop Rating</label>
                        <div class="d-flex align-items-center">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="fa{{ $i <= ($review->rating_store ?? 0) ? 's' : 'r' }} fa-star {{ $i <= ($review->rating_store ?? 0) ? 'text-warning' : 'text-muted' }} mr-1"></i>
                            @endfor
                            <span class="ml-2 text-dark font-weight-bold">{{ $review->rating_store ?? 0 }}/5</span>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="small text-uppercase text-muted font-weight-bold d-block mb-2">Staff Rating</label>
                        <div class="d-flex align-items-center">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="fa{{ $i <= ($review->rating_staff ?? 0) ? 's' : 'r' }} fa-star {{ $i <= ($review->rating_staff ?? 0) ? 'text-warning' : 'text-muted' }} mr-1"></i>
                            @endfor
                            <span class="ml-2 text-dark font-weight-bold">{{ $review->rating_staff ?? 0 }}/5</span>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="small text-uppercase text-muted font-weight-bold d-block mb-2">Phone No</label>
                        <div class="text-dark">
                            {{ $review->customer_phone ?? '-' }}
                            @if ($review->contact_requested)
                                <span class="badge badge-info ml-2">{{ __('messages.wants_contact') }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card card-light mt-2">
                    <div class="card-header border-0 pb-0">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-quote-left mr-2"></i>Review Comment
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0 text-dark whitespace-pre-wrap">{{ $review->comment ?? 'No review text provided.' }}</p>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="small text-uppercase text-muted font-weight-bold d-block mb-2">Reviewed Date</label>
                    <div class="text-dark">{{ $review->created_at ? $review->created_at->format('d M Y h:i A') : '-' }}</div>
                </div>
            </div>
        </div>

        @can('update', $review)
            <div class="card shadow-sm border-0">
                <div class="card-header">
                    <h3 class="card-title mb-0">{{ __('messages.admin_remark') }}</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('review.triage', $review) }}">
                        @csrf
                        @method('PATCH')

                        <div class="form-group">
                            <textarea name="admin_remark" id="admin_remark" class="form-control" rows="4">{{ old('admin_remark', $review->admin_remark) }}</textarea>
                            <small class="form-text text-muted">{{ __('messages.admin_remark_help') }}</small>
                            @if ($review->remarkedBy && $review->remarked_at)
                                <small class="d-block text-muted mt-1">
                                    {{ __('messages.remarked_by', ['name' => $review->remarkedBy->name, 'date' => $review->remarked_at->format('d M Y h:i A')]) }}
                                </small>
                            @endif
                        </div>

                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <a href="{{ route('notes.create', ['review_id' => $review->id, 'store_id' => $review->store_id, 'staff_id' => $review->staff_id]) }}" class="btn btn-outline-primary mb-2">
                                <i class="far fa-sticky-note mr-1"></i>{{ __('messages.create_note_from_review') }}
                            </a>
                            <button type="submit" class="btn btn-primary mb-2">{{ __('messages.save_remark') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        @endcan
    </div>
@endsection
