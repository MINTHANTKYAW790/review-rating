<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $store->name }} - Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .star-rating {
            direction: rtl;
            /* This is the magic: flip display so stars go left-to-right visually */
            display: inline-flex;
            justify-content: center;
        }

        .star-rating input[type="radio"] {
            display: none;
        }

        .star-rating label {
            font-size: 2rem;
            color: #ccc;
            cursor: pointer;
            transition: color 0.2s;
        }

        /* Hover highlight */
        .star-rating label:hover,
        .star-rating label:hover~label {
            color: #ffc107;
        }

        /* Checked highlight */
        .star-rating input:checked~label {
            color: #ffc107;
        }



        .staff-img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #dee2e6;
        }

        .staff-option input[type="radio"] {
            display: none;
        }

        .staff-option input[type="radio"]:checked+label {
            border-color: #0d6efd;
        }

        .staff-option label {
            cursor: pointer;
            padding: 5px;
            display: block;
            border: 2px solid transparent;
            border-radius: 10px;
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <div class="text-center mb-5">
            <img src="{{ asset($store->logo_url ?? 'default-logo.png') }}" alt="{{ $store->name }}" class="mb-3 rounded-circle shadow" width="100">
            <h2 class="fw-bold">{{ $store->name }}</h2>
            @if($store->description)
            <p class="text-muted">{{ $store->description }}</p>
            @endif
        </div>

        <form method="POST" action="{{ route('public.submit-review', $store->slug) }}">
            @csrf

            <!-- Store Rating -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title mb-3">Rate the Store <span class="text-danger">*</span></h5>
                    <div class="star-rating text-center mb-3">
                        @for ($i = 5; $i >= 1; $i--)
                        <input type="radio" name="rating_store" value="{{ $i }}" id="store_star{{ $i }}" required>
                        <label for="store_star{{ $i }}">&#9733;</label>
                        @endfor
                    </div>
                    <textarea class="form-control" name="comment" rows="3" placeholder="Share your thoughts about the store..." required></textarea>
                </div>
            </div>

            <!-- Staff Section -->
            @if ($store->staff->count())
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title mb-3">Rate a Staff Member (Optional)</h5>

                    <div class="d-flex overflow-auto mb-4">
                        @foreach ($store->staff as $member)
                        <div class="text-center mx-3 staff-option">
                            <input type="radio" name="staff_id" value="{{ $member->id }}" id="staff{{ $member->id }}">
                            <label for="staff{{ $member->id }}">
                                <img src="{{ asset($member->image_url ?? 'default-user.png') }}" class="staff-img mb-2" alt="{{ $member->name }}">
                                <div class="small">{{ $member->name }}</div>
                            </label>
                        </div>
                        @endforeach
                    </div>

                    <label class="form-label">Staff Rating</label>
                    <div class="star-rating text-center mb-3">
                        @for ($i = 5; $i >= 1; $i--)
                        <input type="radio" name="rating_staff" value="{{ $i }}" id="staff_rating{{ $i }}">
                        <label for="staff_rating{{ $i }}">&#9733;</label>
                        @endfor
                    </div>

                    <textarea class="form-control" name="comment_staff" rows="2" placeholder="Comment about the staff (optional)..."></textarea>
                </div>
            </div>
            @endif

            <!-- Submit -->
            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg px-5">Submit Feedback</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>