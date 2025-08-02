@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="text-center mb-4">
        <img src="{{ asset($store->logo_url ?? 'default-logo.png') }}" alt="{{ $store->name }}" width="80" class="mb-3">
        <h2>{{ $store->name }}</h2>
        <p>{{ $store->description }}</p>
    </div>

    <form method="POST" action="{{ route('public.submit-review', $store->slug) }}">
        @csrf

        {{-- Store rating --}}
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Rate the Store</h5>
                <div class="mb-3">
                    @for ($i = 1; $i <= 5; $i++)
                        <input type="radio" name="rating_store" value="{{ $i }}" id="store_star{{ $i }}" required>
                        <label for="store_star{{ $i }}">⭐</label>
                    @endfor
                </div>
                <textarea class="form-control" name="comment" rows="3" placeholder="Share your thoughts..."></textarea>
            </div>
        </div>

        {{-- Staff list --}}
        @if ($store->staff->count())
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Rate a Staff Member (Optional)</h5>
                <div class="d-flex flex-row overflow-auto mb-3">
                    @foreach ($store->staff as $member)
                        <div class="text-center mx-3">
                            <img src="{{ asset($member->image_url ?? 'default-user.png') }}" class="rounded-circle" width="60">
                            <div>
                                <input type="radio" name="staff_id" value="{{ $member->id }}" id="staff{{ $member->id }}">
                                <label for="staff{{ $member->id }}">{{ $member->name }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mb-3">
                    <label>Staff Rating</label><br>
                    @for ($i = 1; $i <= 5; $i++)
                        <input type="radio" name="rating_staff" value="{{ $i }}" id="staff_rating{{ $i }}">
                        <label for="staff_rating{{ $i }}">⭐</label>
                    @endfor
                </div>
                <textarea class="form-control" name="comment_staff" rows="2" placeholder="Comment about the staff..."></textarea>
            </div>
        </div>
        @endif

        <div class="text-center">
            <button type="submit" class="btn btn-primary btn-lg">Submit Feedback</button>
        </div>
    </form>
</div>
@endsection
