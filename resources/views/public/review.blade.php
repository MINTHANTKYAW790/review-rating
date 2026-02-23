<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title id="pageTitle">{{ $store->name }} - {{ __('messages.feedback') }}</title>
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
        /* Message bubble styling */
        .message-suggestions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .message-bubble {
            background-color: #e2e6ea;
            border-radius: 20px;
            padding: 8px 15px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background-color 0.2s;
        }

        .message-bubble:hover {
            background-color: #d3d7da;
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <!-- Language Toggle -->
        <div class="d-flex justify-content-end mb-4">
            <button class="btn btn-sm btn-outline-primary me-2" data-locale="en">English</button>
            <button class="btn btn-sm btn-outline-primary" data-locale="my">Burmese</button>
        </div>

        <div class="text-center mb-5">
            <img src="{{ $store->logo_url ? asset('storage/' .$store->logo_url) : asset('default-user.jpg') }}" alt="{{ $store->name }}" class="mb-3 rounded-circle shadow" width="100">
            <h2 class="fw-bold">{{ $store->name }}</h2>
            @if($store->description)
            <p class="text-muted" data-translate="store_description">{{ $store->description }}</p>
            @endif
        </div>

        <form method="POST" action="{{ route('public.submit-review', $store->slug) }}">
            @csrf

            <!-- Store Rating -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title mb-3" data-translate="rate_the_store">{{ __('messages.rate_the_store') }} <span class="text-danger">*</span></h5>
                    <div class="star-rating text-center mb-3">
                        @for ($i = 5; $i >= 1; $i--)
                        <input type="radio" name="rating_store" value="{{ $i }}" id="store_star{{ $i }}" {{ $i == 5 ? 'checked' : '' }} required>
                        <label for="store_star{{ $i }}">&#9733;</label>
                        @endfor
                    </div>
                    <textarea class="form-control" name="comment" id="store_comment" rows="3" placeholder="{{ __('messages.store_comment_placeholder') }}" required data-translate-placeholder="store_comment_placeholder"></textarea>
                    <div id="store_message_suggestions" class="message-suggestions"></div>
                </div>
            </div>

            <!-- Staff Section -->
            @if ($store->staff->count())
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title mb-3" data-translate="rate_staff_member_optional">{{ __('messages.rate_staff_member_optional') }}</h5>

                    <div class="d-flex overflow-auto mb-4">
                        @foreach ($store->staff as $member)
                        <div class="text-center mx-3 staff-option">
                            <input type="radio" name="staff_id" value="{{ $member->id }}" id="staff{{ $member->id }}">
                            <label for="staff{{ $member->id }}">
                                <img src="{{ $member->image_url ? asset('storage/' . $member->image_url) : asset('default-user.jpg') }}" class="staff-img mb-2" alt="{{ $member->name }}">
                                <div class="small">{{ $member->name }}</div>
                            </label>
                        </div>
                        @endforeach
                    </div>

                    <label class="form-label" data-translate="staff_rating">{{ __('messages.staff_rating') }}</label>
                    <div class="star-rating text-center mb-3">
                        @for ($i = 5; $i >= 1; $i--)
                        <input type="radio" name="rating_staff" value="{{ $i }}" id="staff_rating{{ $i }}" {{ $i == 5 ? 'checked' : '' }}>
                        <label for="staff_rating{{ $i }}">&#9733;</label>
                        @endfor
                    </div>

                    <textarea class="form-control" name="comment_staff" id="staff_comment" rows="2" placeholder="{{ __('messages.staff_comment_placeholder') }}" data-translate-placeholder="staff_comment_placeholder"></textarea>
                    <div id="staff_message_suggestions" class="message-suggestions"></div>
                </div>
            </div>
            @endif

            <!-- Submit -->
            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg px-5" data-translate="submit_feedback">{{ __('messages.submit_feedback') }}</button>
            </div>
        </form>
    </div>

    <!-- Feedback Status Modal -->
    <div class="modal fade" id="feedbackStatusModal" tabindex="-1" aria-labelledby="feedbackStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="feedbackStatusModalLabel" data-translate="thank_you_so_much">{{ __('messages.thank_you_so_much') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('messages.close') }}" data-translate-aria-label="close"></button>
                </div>
                <div class="modal-body" id="feedbackStatusModalBody">
                    <!-- Message will be inserted here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-translate="close">{{ __('messages.close') }}</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load all translations for JavaScript
        const translations = {
            en: {
                feedback: "{{ __('messages.feedback', [], 'en') }}",
                rate_the_store: "{{ __('messages.rate_the_store', [], 'en') }}",
                store_comment_placeholder: "{{ __('messages.store_comment_placeholder', [], 'en') }}",
                rate_staff_member_optional: "{{ __('messages.rate_staff_member_optional', [], 'en') }}",
                staff_rating: "{{ __('messages.staff_rating', [], 'en') }}",
                staff_comment_placeholder: "{{ __('messages.staff_comment_placeholder', [], 'en') }}",
                submit_feedback: "{{ __('messages.submit_feedback', [], 'en') }}",
                thank_you_so_much: "{{ __('messages.thank_you_so_much', [], 'en') }}",
                close: "{{ __('messages.close', [], 'en') }}",
                positive_messages: @json(__('messages.positive_messages', [], 'en')),
                negative_messages: @json(__('messages.negative_messages', [], 'en'))
            },
            my: {
                feedback: "{{ __('messages.feedback', [], 'my') }}",
                rate_the_store: "{{ __('messages.rate_the_store', [], 'my') }}",
                store_comment_placeholder: "{{ __('messages.store_comment_placeholder', [], 'my') }}",
                rate_staff_member_optional: "{{ __('messages.rate_staff_member_optional', [], 'my') }}",
                staff_rating: "{{ __('messages.staff_rating', [], 'my') }}",
                staff_comment_placeholder: "{{ __('messages.staff_comment_placeholder', [], 'my') }}",
                submit_feedback: "{{ __('messages.submit_feedback', [], 'my') }}",
                thank_you_so_much: "{{ __('messages.thank_you_so_much', [], 'my') }}",
                close: "{{ __('messages.close', [], 'my') }}",
                positive_messages: @json(__('messages.positive_messages', [], 'my')),
                negative_messages: @json(__('messages.negative_messages', [], 'my'))
            }
        };

        // These variables need to be globally accessible for setLanguage and displayMessageSuggestions
        let currentLocale = localStorage.getItem('review_locale') || 'my'; // Default to Burmese
        
        // Removed globalPositiveMessages and globalNegativeMessages, will pass directly

        function getRandomMessages(messages, count) {
            const shuffled = [...messages].sort(() => 0.5 - Math.random());
            return shuffled.slice(0, count);
        }

        // Updated displayMessageSuggestions to accept messages directly
        function displayMessageSuggestions(rating, targetSuggestionsElement, targetCommentElement, positiveMsgs, negativeMsgs) {
            targetSuggestionsElement.innerHTML = ''; // Clear previous suggestions
            // Use the passed message arrays
            const messages = rating >= 3 ? positiveMsgs : negativeMsgs;
            const messagesToDisplay = getRandomMessages(messages, 4); // Display 4 random messages

            messagesToDisplay.forEach(msg => {
                const bubble = document.createElement('span');
                bubble.classList.add('message-bubble');
                bubble.textContent = msg;
                bubble.addEventListener('click', () => {
                    targetCommentElement.value = msg;
                });
                targetSuggestionsElement.appendChild(bubble);
            });
        }


        function setLanguage(locale) {
            currentLocale = locale;
            localStorage.setItem('review_locale', locale);

            // Update page title
            document.getElementById('pageTitle').textContent = `{{ $store->name }} - ${translations[locale].feedback}`;

            // Update static text elements
            document.querySelectorAll('[data-translate]').forEach(el => {
                const key = el.getAttribute('data-translate');
                if (translations[locale][key]) {
                    el.textContent = translations[locale][key];
                }
            });

            // Update placeholder text
            document.querySelectorAll('[data-translate-placeholder]').forEach(el => {
                const key = el.getAttribute('data-translate-placeholder');
                if (translations[locale][key]) {
                    el.placeholder = translations[locale][key];
                }
            });

            // Update aria-label
            document.querySelectorAll('[data-translate-aria-label]').forEach(el => {
                const key = el.getAttribute('data-translate-aria-label');
                if (translations[locale][key]) {
                    el.setAttribute('aria-label', translations[locale][key]);
                }
            });

            // Re-render message suggestions if any stars are checked, passing current locale messages
            const storeComment = document.getElementById('store_comment');
            const storeMessageSuggestions = document.getElementById('store_message_suggestions');
            const initialStoreRating = document.querySelector('input[name="rating_store"]:checked');
            if (initialStoreRating) {
                displayMessageSuggestions(parseInt(initialStoreRating.value), storeMessageSuggestions, storeComment, translations[locale].positive_messages, translations[locale].negative_messages);
            }

            const staffComment = document.getElementById('staff_comment');
            const staffMessageSuggestions = document.getElementById('staff_message_suggestions');
            const initialStaffRating = document.querySelector('input[name="rating_staff"]:checked');
            if (initialStaffRating) {
                displayMessageSuggestions(parseInt(initialStaffRating.value), staffMessageSuggestions, staffComment, translations[locale].positive_messages, translations[locale].negative_messages);
            }

            // Update button active state
            document.querySelectorAll('.d-flex.justify-content-end.mb-4 button').forEach(button => {
                if (button.getAttribute('data-locale') === locale) {
                    button.classList.add('active');
                } else {
                    button.classList.remove('active');
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            setLanguage(currentLocale); // Set language on initial load

            // Attach event listeners to language toggle buttons
            document.querySelectorAll('.d-flex.justify-content-end.mb-4 button').forEach(button => {
                button.addEventListener('click', function() {
                    setLanguage(this.getAttribute('data-locale'));
                });
            });

            // Store Rating Logic (ensure displayMessageSuggestions is called initially and on change)
            const storeStarInputs = document.querySelectorAll('input[name="rating_store"]');
            const storeComment = document.getElementById('store_comment');
            const storeMessageSuggestions = document.getElementById('store_message_suggestions');

            if (storeStarInputs.length > 0) {
                // Ensure suggestions are displayed based on initial (or default) locale
                const initialStoreRating = document.querySelector('input[name="rating_store"]:checked');
                if (initialStoreRating) {
                    displayMessageSuggestions(parseInt(initialStoreRating.value), storeMessageSuggestions, storeComment, translations[currentLocale].positive_messages, translations[currentLocale].negative_messages);
                }

                storeStarInputs.forEach(input => {
                    input.addEventListener('change', function() {
                        displayMessageSuggestions(parseInt(this.value), storeMessageSuggestions, storeComment, translations[currentLocale].positive_messages, translations[currentLocale].negative_messages);
                    });
                });
            }


            // Staff Rating Logic (if staff section exists)
            const staffStarInputs = document.querySelectorAll('input[name="rating_staff"]');
            const staffComment = document.getElementById('staff_comment');
            const staffMessageSuggestions = document.getElementById('staff_message_suggestions');

            if (staffStarInputs.length > 0) {
                // Ensure suggestions are displayed based on initial (or default) locale
                const initialStaffRating = document.querySelector('input[name="rating_staff"]:checked');
                if (initialStaffRating) {
                    displayMessageSuggestions(parseInt(initialStaffRating.value), staffMessageSuggestions, staffComment, translations[currentLocale].positive_messages, translations[currentLocale].negative_messages);
                }

                staffStarInputs.forEach(input => {
                    input.addEventListener('change', function() {
                        displayMessageSuggestions(parseInt(this.value), staffMessageSuggestions, staffComment, translations[currentLocale].positive_messages, translations[currentLocale].negative_messages);
                    });
                });
            }


            // --- Feedback Status Modal Logic ---
            const feedbackStatusModalElement = document.getElementById('feedbackStatusModal');
            if (feedbackStatusModalElement) {
                const feedbackStatusModal = new bootstrap.Modal(feedbackStatusModalElement);
                const feedbackStatusModalBody = document.getElementById('feedbackStatusModalBody');

                const statusMessage = "{{ session('status') }}";
                if (statusMessage) {
                    feedbackStatusModalBody.textContent = statusMessage;
                    feedbackStatusModal.show();
                }
            }
        });
    </script>
</body>

</html>