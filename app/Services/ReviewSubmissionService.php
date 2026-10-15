<?php

namespace App\Services;

use App\Events\ReviewCreated;
use App\Models\Review;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReviewSubmissionService
{
    /**
     * @return array{redirect: \Illuminate\Http\RedirectResponse}
     */
    public function submit(Request $request, Store $store, string $slug): array
    {
        if (! $store->is_active) {
            return [
                'redirect' => redirect()
                    ->route('public.review', $slug)
                    ->with('review_error', 'This store is not accepting reviews right now.'),
            ];
        }

        if ($store->hasReachedReviewCap()) {
            return [
                'redirect' => redirect()
                    ->route('public.review', $slug)
                    ->with('review_error', __('messages.review_limit_reached')),
            ];
        }

        $validated = $request->validate([
            'rating_store' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'staff_id' => 'nullable|exists:staff,id',
            'rating_staff' => 'nullable|integer|min:1|max:5',
            'comment_staff' => 'nullable|string',
            'customer_phone' => 'nullable|string|max:20',
            'contact_requested' => 'nullable|boolean',
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image_url')) {
            $imagePath = $request->file('image_url')->store('review_images', 'public');
        }

        $deviceCookieName = 'reviewer_device_id';
        $deviceCookieValue = $request->cookie($deviceCookieName) ?: (string) Str::uuid();

        $ipAddress = $request->ip() ?? $request->server('REMOTE_ADDR');
        $todayStart = Carbon::today()->startOfDay();
        $todayEnd = Carbon::today()->endOfDay();

        $duplicateReviewExists = DB::table('reviews')
            ->where('store_id', $store->id)
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->where(function ($query) use ($ipAddress, $deviceCookieValue) {
                $query->where('ip_address', $ipAddress)
                    ->orWhere('device_cookie', $deviceCookieValue);
            })
            ->exists();

        if ($duplicateReviewExists) {
            return [
                'redirect' => back()
                    ->withInput()
                    ->withErrors([
                        'review' => 'You have already submitted a review today.',
                    ])
                    ->with('review_error', 'You have already submitted a review today. Please try again tomorrow.'),
            ];
        }

        $staffId = $validated['staff_id'] ?? null;
        $ratingStaff = $staffId ? ($validated['rating_staff'] ?? null) : null;

        $contactRequested = $request->boolean('contact_requested');

        $isDemo = ($store->relationLoaded('user') ? $store->user?->email : $store->user()->value('email')) === 'demo@gmail.test'
            || str_contains(strtolower($store->name), 'demo');

        $review = Review::create([
            'store_id' => $store->id,
            'staff_id' => $staffId,
            'rating_store' => $validated['rating_store'],
            'rating_staff' => $ratingStaff,
            'comment' => $validated['comment'] ?? null,
            'comment_staff' => $validated['comment_staff'] ?? null,
            'customer_phone' => $validated['customer_phone'] ?? null,
            'contact_requested' => $contactRequested,
            'follow_up_status' => Review::STATUS_INBOX,
            'image_url' => $imagePath,
            'ip_address' => $ipAddress,
            'device_cookie' => $deviceCookieValue,
            'is_demo' => $isDemo,
        ]);

        event(new ReviewCreated($review, (int) $store->user_id));

        $message = ((int) $validated['rating_store'] >= 3)
            ? 'Thank you for your valuable feedback! We appreciate your high rating and are glad you had a positive experience.'
            : 'We are very sorry to hear that your experience was not satisfactory. We appreciate your honest feedback and will strive to do our best to improve. Thank you for your patience.';

        $cookie = Cookie::make($deviceCookieName, $deviceCookieValue, 60 * 24 * 365, '/', null, false, true);

        return [
            'redirect' => redirect()->route('public.review', $slug)
                ->with('status', $message)
                ->withCookie($cookie),
        ];
    }
}
