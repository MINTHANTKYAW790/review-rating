<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title id="pageTitle">{{ $store->name }} - {{ __('messages.feedback') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --page-bg: #f5f7fb;
            --card-bg: #ffffff;
            --card-border: #edf1f7;
            --text-strong: #111827;
            --text-muted: #6b7280;
            --input-bg: #f9fafb;
            --chip-bg: #f3f4f6;
            --chip-border: #e5e7eb;
            --accent: #111827;
            --shadow: 0 24px 60px rgba(15, 23, 42, 0.10);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Inter, "Segoe UI", sans-serif;
            background: linear-gradient(180deg, #f7f9fc 0%, #eef4ff 100%);
            color: var(--text-strong);
        }

        .feedback-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 28px 16px;
        }

        .feedback-shell {
            width: 100%;
            max-width: 480px;
        }

        .language-switcher {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-bottom: 14px;
        }

        .lang-btn {
            border: 1px solid rgba(17, 24, 39, 0.12);
            background: rgba(255, 255, 255, 0.7);
            color: var(--text-muted);
            border-radius: 999px;
            padding: 8px 14px;
            font-size: 0.78rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .lang-btn.active,
        .lang-btn:hover {
            background: var(--accent);
            color: #fff;
            border-color: var(--accent);
            box-shadow: 0 8px 18px rgba(17, 24, 39, 0.18);
        }

        .feedback-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 28px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .feedback-header {
            background: #f8fafc;
        }

        .store-banner-wrap {
            padding: 14px 14px 0;
        }

        .store-banner {
            width: 100%;
            height: 220px;
            object-fit: cover;
            display: block;
            border-radius: 20px;
            aspect-ratio: 16 / 9;
        }

        .store-meta {
            text-align: center;
            padding: 18px 24px 10px;
        }

        .eyebrow {
            margin: 0 0 8px;
            font-size: 0.72rem;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--text-muted);
            font-weight: 700;
        }

        .store-meta h1 {
            margin: 0;
            font-size: clamp(1.8rem, 4vw, 2.3rem);
            line-height: 1.2;
            font-weight: 800;
            letter-spacing: -0.04em;
        }

        .divider {
            margin: 0 24px;
            border-top: 1px dashed #dfe5ee;
        }

        .feedback-body {
            padding: 22px 24px 8px;
        }

        .section-head h2 {
            margin: 0 0 6px;
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        .section-head p {
            margin: 0;
            color: var(--text-muted);
            font-size: 0.93rem;
            line-height: 1.5;
        }

        .rating-group {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 8px;
            margin-top: 18px;
        }

        .rating-option {
            border: 1px solid var(--chip-border);
            background: var(--input-bg);
            border-radius: 18px;
            min-height: 98px;
            padding: 12px 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            color: var(--text-strong);
        }

        .rating-option:hover {
            transform: translateY(-1px);
            border-color: #d1d5db;
        }

        .rating-option .emoji {
            font-size: 1.9rem;
            line-height: 1;
        }

        .rating-option .rating-label {
            font-size: 0.67rem;
            font-weight: 600;
            color: var(--text-muted);
            text-align: center;
            line-height: 1.3;
        }

        .rating-option.selected {
            border-width: 1.5px;
            box-shadow: 0 12px 24px rgba(17, 24, 39, 0.08);
        }

        .rating-option[data-rating="1"].selected {
            background: #fee2e2;
            border-color: #ef4444;
        }

        .rating-option[data-rating="1"].selected .emoji,
        .rating-option[data-rating="1"].selected .rating-label {
            color: #b91c1c;
        }

        .rating-option[data-rating="2"].selected {
            background: #ffedd5;
            border-color: #f59e0b;
        }

        .rating-option[data-rating="2"].selected .emoji,
        .rating-option[data-rating="2"].selected .rating-label {
            color: #b45309;
        }

        .rating-option[data-rating="3"].selected {
            background: #fef3c7;
            border-color: #eab308;
        }

        .rating-option[data-rating="3"].selected .emoji,
        .rating-option[data-rating="3"].selected .rating-label {
            color: #a16207;
        }

        .rating-option[data-rating="4"].selected {
            background: #dcfce7;
            border-color: #22c55e;
        }

        .rating-option[data-rating="4"].selected .emoji,
        .rating-option[data-rating="4"].selected .rating-label {
            color: #15803d;
        }

        .rating-option[data-rating="5"].selected {
            background: #e0e7ff;
            border-color: #6366f1;
        }

        .rating-option[data-rating="5"].selected .emoji,
        .rating-option[data-rating="5"].selected .rating-label {
            color: #4338ca;
        }

        .field-wrap {
            margin-top: 20px;
        }

        .field-wrap label {
            display: block;
            font-size: 0.9rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--text-strong);
        }

        textarea {
            width: 100%;
            border: 1px solid var(--chip-border);
            background: var(--input-bg);
            border-radius: 18px;
            min-height: 120px;
            resize: vertical;
            padding: 14px 16px;
            font-size: 0.97rem;
            color: var(--text-strong);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        textarea:focus {
            outline: none;
            border-color: rgba(17, 24, 39, 0.4);
            box-shadow: 0 0 0 4px rgba(17, 24, 39, 0.05);
        }

        .char-counter {
            display: flex;
            justify-content: flex-end;
            margin-top: 8px;
            color: var(--text-muted);
            font-size: 0.77rem;
            font-weight: 600;
        }

        .optional-contact-block {
            margin-top: 18px;
        }

        .optional-input {
            width: 100%;
            border: 1px solid var(--chip-border);
            background: var(--input-bg);
            border-radius: 14px;
            min-height: 48px;
            padding: 12px 14px;
            font-size: 0.95rem;
            color: var(--text-strong);
        }

        .form-check {
            margin-top: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
            margin-top: 0;
            accent-color: #111827;
        }

        .form-check-label {
            font-size: 0.9rem;
            color: var(--text-strong);
            cursor: pointer;
            line-height: 1.5;
        }

        .tag-section {
            margin-top: 22px;
        }

        .tag-section p {
            margin: 0 0 10px;
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--text-strong);
        }

        .tag-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .tag-chip {
            border: 1px solid var(--chip-border);
            background: var(--chip-bg);
            color: #374151;
            border-radius: 999px;
            padding: 9px 12px;
            font-size: 0.79rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .tag-chip:hover {
            border-color: #cbd5e1;
        }

        .tag-chip.selected {
            background: #eef2ff;
            color: #3730a3;
            border-color: #a5b4fc;
            box-shadow: 0 8px 18px rgba(99, 102, 241, 0.12);
        }

        .visually-hidden-rating {
            position: absolute;
            opacity: 0;
            pointer-events: none;
            width: 0;
            height: 0;
            margin: 0;
        }

        .staff-section {
            margin-top: 22px;
        }

        .section-head-sm {
            margin-bottom: 12px;
        }

        .staff-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(90px, 1fr));
            gap: 12px;
            margin-top: 16px;
        }

        .staff-option {
            text-align: center;
        }

        .staff-option input[type="radio"] {
            display: none;
        }

        .staff-option label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 8px;
            border: 1px solid var(--chip-border);
            background: var(--input-bg);
            border-radius: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            min-height: 110px;
            width: 100%;
        }

        .staff-option input[type="radio"]:checked + label {
            border-color: #0d6efd;
            box-shadow: 0 10px 18px rgba(59, 130, 246, 0.12);
            background: #eff6ff;
        }

        .staff-img {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e5e7eb;
        }

        .staff-option label span {
            font-size: 0.76rem;
            font-weight: 600;
            color: var(--text-strong);
        }

        .stepper-dots {
            display: flex;
            justify-content: center;
            gap: 8px;
            padding: 4px 0 12px;
        }

        .step-dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: #d1d5db;
            transition: all 0.2s ease;
        }

        .step-dot.active {
            width: 22px;
            background: #111827;
        }

        .review-step[hidden] {
            display: none !important;
        }

        .staff-rating-fields[hidden] {
            display: none !important;
        }

        .step-nav {
            position: sticky;
            bottom: 0;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 24px 24px;
            margin-top: 18px;
            background: linear-gradient(180deg, rgba(255,255,255,0) 0%, #fff 18%);
        }

        .step-btn {
            border: 1px solid var(--chip-border);
            background: #fff;
            color: var(--text-strong);
            border-radius: 18px;
            padding: 14px 16px;
            font-size: 0.95rem;
            font-weight: 700;
            min-height: 52px;
            cursor: pointer;
        }

        .step-btn-back {
            flex: 0 0 auto;
            min-width: 96px;
        }

        .step-btn-next,
        .submit-btn {
            flex: 1;
            border: none;
            border-radius: 18px;
            background: #111827;
            color: #fff;
            padding: 16px 18px;
            font-size: 1rem;
            font-weight: 700;
            min-height: 52px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .submit-btn:hover,
        .step-btn-next:hover {
            background: #0f172a;
            transform: translateY(-1px);
            box-shadow: 0 14px 28px rgba(17, 24, 39, 0.18);
        }

        .step-btn[hidden],
        .submit-btn[hidden] {
            display: none !important;
        }

        .custom-feedback-modal .modal-dialog {
            max-width: 420px;
            margin: 1.75rem auto;
        }

        .custom-feedback-modal .modal-content {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .custom-feedback-modal .modal-header {
            border-bottom: 1px dashed #dfe5ee;
            padding: 1.1rem 1.2rem 0.9rem;
            background: #f8fafc;
        }

        .custom-feedback-modal .modal-title {
            font-size: 1.15rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--text-strong);
            margin: 0;
        }

        .custom-feedback-modal .btn-close {
            font-size: 0.8rem;
            padding: 0.5rem;
        }

        .custom-feedback-modal .modal-body {
            padding: 1.2rem 1.2rem 1.1rem;
            color: var(--text-strong);
            font-size: 0.96rem;
            line-height: 1.6;
            background: #fff;
        }

        .custom-feedback-modal .modal-footer {
            border-top: 0;
            padding: 0 1.2rem 1.1rem;
            background: #fff;
        }

        .custom-feedback-modal .modal-footer .btn {
            border-radius: 12px;
            padding: 0.7rem 1.1rem;
            font-weight: 700;
            min-width: 110px;
        }

        @media (max-width: 420px) {
            .feedback-page {
                padding: 18px 12px;
            }

            .feedback-body,
            .step-nav {
                padding-left: 16px;
                padding-right: 16px;
            }

            .store-banner-wrap {
                padding-left: 10px;
                padding-right: 10px;
            }

            .store-banner {
                height: 180px;
            }

            .rating-group {
                gap: 6px;
            }

            .rating-option {
                min-height: 88px;
                padding: 10px 6px;
            }
        }
    </style>
</head>

<body>
    <div class="feedback-page">
        <div class="feedback-shell">
            <div class="language-switcher" aria-label="Language selector">
                <button type="button" class="lang-btn active" data-locale="en">English</button>
                <button type="button" class="lang-btn" data-locale="my">မြန်မာ</button>
            </div>

            <form method="POST" action="{{ route('public.submit-review', $store->slug) }}" class="feedback-card" enctype="multipart/form-data">
                @csrf

                <div class="feedback-header">
                    <div class="store-banner-wrap">
                        <img src="{{ $store->logo_url ? asset('storage/' . $store->logo_url) : asset('default-store.jpg') }}" alt="{{ $store->name }}" class="store-banner">
                    </div>

                    <div class="store-meta">
                        <p class="eyebrow" data-translate="feedback">{{ __('messages.feedback') }}</p>
                        <h1>{{ $store->name }}</h1>
                    </div>
                </div>

                <div class="divider"></div>

                @php
                    $hasStaff = $store->staff->count() > 0;
                    $oldStoreRating = (int) old('rating_store', 5);
                    $oldStaffId = old('staff_id');
                    $oldStaffRating = old('rating_staff');
                @endphp

                <div class="feedback-body">
                    <div class="stepper-dots" aria-hidden="true">
                        <span class="step-dot active"></span>
                        @if ($hasStaff)
                            <span class="step-dot"></span>
                        @endif
                        <span class="step-dot"></span>
                    </div>

                    <div class="review-step" data-step="0">
                        <div class="section-head">
                            <h2 data-translate="rate_the_store">{{ __('messages.rate_the_store') }}</h2>
                        </div>

                        <div class="rating-group" role="radiogroup" aria-label="Store rating">
                            @for ($i = 1; $i <= 5; $i++)
                                <input type="radio" name="rating_store" value="{{ $i }}" id="store_rating_{{ $i }}" class="visually-hidden-rating" {{ $oldStoreRating === $i ? 'checked' : '' }}>
                                <label for="store_rating_{{ $i }}" class="rating-option {{ $oldStoreRating === $i ? 'selected' : '' }}" data-rating="{{ $i }}" aria-label="Rate {{ $i }} out of 5" aria-pressed="{{ $oldStoreRating === $i ? 'true' : 'false' }}">
                                    <span class="emoji">{{ ['😡', '😕', '😐', '🙂', '😍'][$i - 1] }}</span>
                                    <span class="rating-label">{{ ['Very poor', 'Poor', 'Okay', 'Good', 'Excellent'][$i - 1] }}</span>
                                </label>
                            @endfor
                        </div>

                        <div class="field-wrap">
                            <label for="store_comment" data-translate="store_comment_placeholder">{{ __('messages.store_comment_placeholder') }}</label>
                            <textarea id="store_comment" name="comment" rows="5" maxlength="1000" placeholder="{{ __('messages.store_comment_placeholder') }}" data-translate-placeholder="store_comment_placeholder">{{ old('comment') }}</textarea>
                            <div class="char-counter"><span id="char_count">0</span> / 1000</div>
                            <div id="store_message_suggestions" class="tag-list message-suggestions" aria-label="Suggested review messages"></div>
                        </div>
                    </div>

                    @if ($hasStaff)
                        <div class="review-step" data-step="1" hidden>
                            <div class="section-head section-head-sm">
                                <h2 data-translate="rate_staff_member_optional">{{ __('messages.rate_staff_member_optional') }}</h2>
                            </div>

                            <div class="staff-list">
                                @foreach ($store->staff as $member)
                                    <div class="staff-option">
                                        <input type="radio" name="staff_id" value="{{ $member->id }}" id="staff{{ $member->id }}" {{ (string) $oldStaffId === (string) $member->id ? 'checked' : '' }}>
                                        <label for="staff{{ $member->id }}">
                                            <img src="{{ $member->image_url ? asset('storage/' . $member->image_url) : asset('default-user.jpg') }}" class="staff-img" alt="{{ $member->name }}">
                                            <span>{{ $member->name }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            <div class="staff-rating-fields" @if (! $oldStaffId) hidden @endif>
                                <div class="field-wrap">
                                    <label data-translate="staff_rating">{{ __('messages.staff_rating') }}</label>
                                    <div class="rating-group staff-rating-group" role="radiogroup" aria-label="Staff rating">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <input type="radio" name="rating_staff" value="{{ $i }}" id="staff_rating_{{ $i }}" class="visually-hidden-rating" {{ (int) $oldStaffRating === $i ? 'checked' : '' }}>
                                            <label for="staff_rating_{{ $i }}" class="rating-option staff-rating-option {{ (int) $oldStaffRating === $i ? 'selected' : '' }}" data-rating="{{ $i }}" aria-label="Rate staff {{ $i }} out of 5" aria-pressed="{{ (int) $oldStaffRating === $i ? 'true' : 'false' }}">
                                                <span class="emoji">{{ ['😡', '😕', '😐', '🙂', '😍'][$i - 1] }}</span>
                                                <span class="rating-label">{{ ['Very poor', 'Poor', 'Okay', 'Good', 'Excellent'][$i - 1] }}</span>
                                            </label>
                                        @endfor
                                    </div>
                                </div>

                                <div class="field-wrap">
                                    <label for="staff_comment" data-translate="staff_comment_placeholder">{{ __('messages.staff_comment_placeholder') }}</label>
                                    <textarea id="staff_comment" name="comment_staff" rows="3" placeholder="{{ __('messages.staff_comment_placeholder') }}" data-translate-placeholder="staff_comment_placeholder">{{ old('comment_staff') }}</textarea>
                                    <div id="staff_message_suggestions" class="tag-list message-suggestions" aria-label="Suggested staff review messages"></div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="review-step" data-step="{{ $hasStaff ? 2 : 1 }}" hidden>
                        <div class="field-wrap optional-contact-block">
                            <label for="customer_phone" data-translate="phone_optional">{{ __('messages.phone_optional') }}</label>
                            <input type="tel" id="customer_phone" name="customer_phone" class="form-control optional-input" placeholder="e.g. 09 123 456 789" value="{{ old('customer_phone') }}" inputmode="tel">
                        </div>

                        <div class="field-wrap optional-contact-block">
                            <label for="review_image" data-translate="upload_photo_optional">{{ __('messages.upload_photo_optional') }}</label>
                            <input type="file" id="review_image" name="image_url" class="form-control optional-input" accept="image/*">
                            <small class="text-muted d-block mt-2" data-translate="photo_upload_hint">{{ __('messages.photo_upload_hint') }}</small>
                        </div>

                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" value="1" id="contact_requested" name="contact_requested" {{ old('contact_requested') ? 'checked' : '' }}>
                            <label class="form-check-label" for="contact_requested" data-translate="contact_me_checkbox">
                                {{ __('messages.contact_me_checkbox') }}
                            </label>
                        </div>
                    </div>
                </div>

                <div class="step-nav">
                    <button type="button" class="step-btn step-btn-back" hidden data-translate="review_back">{{ __('messages.review_back') }}</button>
                    <button type="button" class="step-btn-next" data-translate="review_next">{{ __('messages.review_next') }}</button>
                    <button type="submit" class="submit-btn" hidden data-translate="submit_feedback">{{ __('messages.submit_feedback') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade custom-feedback-modal" id="feedbackStatusModal" tabindex="-1" aria-labelledby="feedbackStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="feedbackStatusModalLabel" data-translate="thank_you_so_much">{{ __('messages.thank_you_so_much') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('messages.close') }}" data-translate-aria-label="close"></button>
                </div>
                <div class="modal-body" id="feedbackStatusModalBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal" data-translate="close">{{ __('messages.close') }}</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const translations = {
            en: {
                feedback: "{{ __('messages.feedback', [], 'en') }}",
                rate_the_store: "{{ __('messages.rate_the_store', [], 'en') }}",
                store_comment_placeholder: "{{ __('messages.store_comment_placeholder', [], 'en') }}",
                rate_staff_member_optional: "{{ __('messages.rate_staff_member_optional', [], 'en') }}",
                staff_rating: "{{ __('messages.staff_rating', [], 'en') }}",
                staff_comment_placeholder: "{{ __('messages.staff_comment_placeholder', [], 'en') }}",
                submit_feedback: "{{ __('messages.submit_feedback', [], 'en') }}",
                review_next: "{{ __('messages.review_next', [], 'en') }}",
                review_back: "{{ __('messages.review_back', [], 'en') }}",
                review_skip: "{{ __('messages.review_skip', [], 'en') }}",
                phone_optional: "{{ __('messages.phone_optional', [], 'en') }}",
                upload_photo_optional: "{{ __('messages.upload_photo_optional', [], 'en') }}",
                photo_upload_hint: "{{ __('messages.photo_upload_hint', [], 'en') }}",
                contact_me_checkbox: "{{ __('messages.contact_me_checkbox', [], 'en') }}",
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
                review_next: "{{ __('messages.review_next', [], 'my') }}",
                review_back: "{{ __('messages.review_back', [], 'my') }}",
                review_skip: "{{ __('messages.review_skip', [], 'my') }}",
                phone_optional: "{{ __('messages.phone_optional', [], 'my') }}",
                upload_photo_optional: "{{ __('messages.upload_photo_optional', [], 'my') }}",
                photo_upload_hint: "{{ __('messages.photo_upload_hint', [], 'my') }}",
                contact_me_checkbox: "{{ __('messages.contact_me_checkbox', [], 'my') }}",
                thank_you_so_much: "{{ __('messages.thank_you_so_much', [], 'my') }}",
                close: "{{ __('messages.close', [], 'my') }}",
                positive_messages: @json(__('messages.positive_messages', [], 'my')),
                negative_messages: @json(__('messages.negative_messages', [], 'my'))
            }
        };

        let currentLocale = localStorage.getItem('review_locale') || 'my';

        function getRandomMessages(messages, count) {
            const shuffled = [...messages].sort(() => 0.5 - Math.random());
            return shuffled.slice(0, count);
        }

        function displayMessageSuggestions(rating, targetSuggestionsElement, targetCommentElement, positiveMsgs, negativeMsgs) {
            if (!targetSuggestionsElement) return;
            targetSuggestionsElement.innerHTML = '';
            const messages = rating >= 3 ? positiveMsgs : negativeMsgs;
            const messagesToDisplay = getRandomMessages(messages, 4);

            messagesToDisplay.forEach(msg => {
                const bubble = document.createElement('button');
                bubble.type = 'button';
                bubble.className = 'tag-chip';
                bubble.textContent = msg;
                bubble.addEventListener('click', () => {
                    targetCommentElement.value = msg;
                    targetCommentElement.focus();
                });
                targetSuggestionsElement.appendChild(bubble);
            });
        }

        function updateRatingSelection(groupName, value) {
            document.querySelectorAll(`input[name="${groupName}"]`).forEach(input => {
                const isSelected = Number(input.value) === Number(value);
                input.checked = isSelected;
            });

            document.querySelectorAll(`label[for^="${groupName === 'rating_store' ? 'store_rating_' : 'staff_rating_'}"]`).forEach(label => {
                const selected = Number(label.dataset.rating) === Number(value);
                label.classList.toggle('selected', selected);
                label.setAttribute('aria-pressed', selected ? 'true' : 'false');
            });
        }

        function setLanguage(locale) {
            currentLocale = locale;
            localStorage.setItem('review_locale', locale);

            document.getElementById('pageTitle').textContent = `{{ $store->name }} - ${translations[locale].feedback}`;

            document.querySelectorAll('[data-translate]').forEach(el => {
                const key = el.getAttribute('data-translate');
                if (translations[locale][key]) {
                    el.textContent = translations[locale][key];
                }
            });

            document.querySelectorAll('[data-translate-placeholder]').forEach(el => {
                const key = el.getAttribute('data-translate-placeholder');
                if (translations[locale][key]) {
                    el.placeholder = translations[locale][key];
                }
            });

            document.querySelectorAll('[data-translate-aria-label]').forEach(el => {
                const key = el.getAttribute('data-translate-aria-label');
                if (translations[locale][key]) {
                    el.setAttribute('aria-label', translations[locale][key]);
                }
            });

            const storeRating = document.querySelector('input[name="rating_store"]:checked');
            const storeComment = document.getElementById('store_comment');
            const storeSuggestions = document.getElementById('store_message_suggestions');
            if (storeRating) {
                displayMessageSuggestions(parseInt(storeRating.value), storeSuggestions, storeComment, translations[locale].positive_messages, translations[locale].negative_messages);
            }

            const staffRating = document.querySelector('input[name="rating_staff"]:checked');
            const staffComment = document.getElementById('staff_comment');
            const staffSuggestions = document.getElementById('staff_message_suggestions');
            if (staffRating) {
                displayMessageSuggestions(parseInt(staffRating.value), staffSuggestions, staffComment, translations[locale].positive_messages, translations[locale].negative_messages);
            }

            document.querySelectorAll('.lang-btn').forEach(button => {
                button.classList.toggle('active', button.getAttribute('data-locale') === locale);
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const storeRadios = document.querySelectorAll('input[name="rating_store"]');
            const staffRadios = document.querySelectorAll('input[name="rating_staff"]');
            const textarea = document.getElementById('store_comment');
            const counter = document.getElementById('char_count');
            const tagButtons = document.querySelectorAll('.tag-chip');
            const langButtons = document.querySelectorAll('.lang-btn');

            function updateCounter() {
                counter.textContent = textarea.value.length;
            }

            storeRadios.forEach(input => {
                input.addEventListener('change', function() {
                    updateRatingSelection('rating_store', this.value);
                    displayMessageSuggestions(parseInt(this.value), document.getElementById('store_message_suggestions'), textarea, translations[currentLocale].positive_messages, translations[currentLocale].negative_messages);
                });
            });

            staffRadios.forEach(input => {
                input.addEventListener('change', function() {
                    updateRatingSelection('rating_staff', this.value);
                    const staffComment = document.getElementById('staff_comment');
                    displayMessageSuggestions(parseInt(this.value), document.getElementById('staff_message_suggestions'), staffComment, translations[currentLocale].positive_messages, translations[currentLocale].negative_messages);
                });
            });

            textarea.addEventListener('input', updateCounter);

            tagButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const tag = button.dataset.tag;
                    const isSelected = button.classList.contains('selected');
                    button.classList.toggle('selected', !isSelected);

                    const target = document.getElementById('store_comment');
                    if (isSelected) {
                        target.value = target.value.replace(new RegExp(`\\s*${tag.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}\\s*`, 'g'), '').trim();
                    } else {
                        const current = target.value.trim();
                        target.value = current ? `${current}, ${tag}` : tag;
                    }

                    updateCounter();
                });
            });

            langButtons.forEach(button => {
                button.addEventListener('click', () => {
                    setLanguage(button.getAttribute('data-locale'));
                    updateNextLabel();
                });
            });

            const feedbackStatusModalElement = document.getElementById('feedbackStatusModal');
            if (feedbackStatusModalElement) {
                const feedbackStatusModalBody = document.getElementById('feedbackStatusModalBody');
                const statusMessage = "{{ session('status') ?: '' }}";
                const reviewErrorMessage = "{{ session('review_error') ?: $errors->first('review') ?: '' }}";
                const finalMessage = reviewErrorMessage || statusMessage;

                if (finalMessage) {
                    feedbackStatusModalBody.textContent = finalMessage;

                    if (window.bootstrap && typeof window.bootstrap.Modal !== 'undefined') {
                        const feedbackStatusModal = window.bootstrap.Modal.getOrCreateInstance(feedbackStatusModalElement);
                        feedbackStatusModal.show();
                    } else {
                        feedbackStatusModalElement.classList.add('show');
                        feedbackStatusModalElement.style.display = 'block';
                        feedbackStatusModalElement.setAttribute('aria-hidden', 'false');
                        document.body.classList.add('modal-open');
                    }
                }
            }

            const steps = Array.from(document.querySelectorAll('.review-step'));
            const dots = Array.from(document.querySelectorAll('.step-dot'));
            const backBtn = document.querySelector('.step-btn-back');
            const nextBtn = document.querySelector('.step-btn-next');
            const submitBtn = document.querySelector('.submit-btn');
            const staffRatingFields = document.querySelector('.staff-rating-fields');
            const staffPickInputs = document.querySelectorAll('input[name="staff_id"]');
            const lastStepIndex = steps.length - 1;
            const hasValidationErrors = {{ $errors->any() ? 'true' : 'false' }};
            const storeRatingError = {{ $errors->has('rating_store') ? 'true' : 'false' }};
            let currentStep = storeRatingError ? 0 : (hasValidationErrors ? lastStepIndex : 0);

            function isStaffStep(index) {
                return steps[index]?.querySelector('input[name="staff_id"]') !== null;
            }

            function staffIsSelected() {
                return Array.from(staffPickInputs).some(input => input.checked);
            }

            function updateNextLabel() {
                if (!nextBtn || nextBtn.hidden) {
                    return;
                }

                const key = isStaffStep(currentStep) && !staffIsSelected() ? 'review_skip' : 'review_next';
                nextBtn.setAttribute('data-translate', key);
                nextBtn.textContent = translations[currentLocale][key] || nextBtn.textContent;
            }

            function showStep(index) {
                currentStep = Math.max(0, Math.min(index, lastStepIndex));
                steps.forEach((step, i) => {
                    step.hidden = i !== currentStep;
                });
                dots.forEach((dot, i) => {
                    dot.classList.toggle('active', i === currentStep);
                });
                if (backBtn) {
                    backBtn.hidden = currentStep === 0;
                }
                if (nextBtn) {
                    nextBtn.hidden = currentStep === lastStepIndex;
                }
                if (submitBtn) {
                    submitBtn.hidden = currentStep !== lastStepIndex;
                }
                updateNextLabel();
                document.querySelector('.feedback-card')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

            staffPickInputs.forEach(input => {
                input.addEventListener('change', () => {
                    if (staffRatingFields) {
                        staffRatingFields.hidden = !staffIsSelected();
                    }
                    if (staffIsSelected() && !document.querySelector('input[name="rating_staff"]:checked')) {
                        const defaultStaffRating = document.getElementById('staff_rating_5');
                        if (defaultStaffRating) {
                            defaultStaffRating.checked = true;
                            updateRatingSelection('rating_staff', 5);
                        }
                    }
                    updateNextLabel();
                });
            });

            nextBtn?.addEventListener('click', () => {
                if (currentStep === 0 && !document.querySelector('input[name="rating_store"]:checked')) {
                    return;
                }
                showStep(currentStep + 1);
            });

            backBtn?.addEventListener('click', () => {
                showStep(currentStep - 1);
            });

            updateCounter();
            setLanguage(currentLocale);
            showStep(currentStep);
        });
    </script>
</body>

</html>