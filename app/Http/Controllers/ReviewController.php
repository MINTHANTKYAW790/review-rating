<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userStoresIds = auth('web')->user()->stores->pluck('id');

        $reviews = Review::with(['staff', 'store']) // eager load related data
            ->whereIn('store_id', $userStoresIds) // Filter by user's store IDs
            ->latest()
            ->paginate(10); // change per-page count as needed

        $unreadCount = Review::whereIn('store_id', $userStoresIds)
            ->whereNull('read_at')
            ->count();

        $reviewChannel = 'user.' . auth('web')->id() . '.reviews';

        return view('review.index', compact('reviews', 'unreadCount', 'reviewChannel'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $review = Review::findOrFail($id);

        // Ensure the review belongs to the authenticated user's store
        if ($review->store_id !== auth('web')->user()->store->id) {
            abort(403);
        }

        $review->delete();

        return redirect()->route('review.index')->with('success', 'Review deleted successfully.');
    }

    public function deleteComment(string $id)
    {
        $review = Review::findOrFail($id);

        // Ensure the review belongs to the authenticated user's store
        if ($review->store_id !== auth('web')->user()->store->id) {
            abort(403);
        }

        $review->comment = null;
        $review->comment_by = null;
        $review->save();

        return redirect()->route('review.index')->with('success', 'Comment deleted successfully.');
    }

    public function markRead(Review $review)
    {
        $user = auth('web')->user();

        $isOwnedByUser = $user->stores()->whereKey($review->store_id)->exists();
        if (! $isOwnedByUser) {
            abort(403);
        }

        if (is_null($review->read_at)) {
            $review->forceFill([
                'read_at' => now(),
            ])->save();
        }

        $userStoreIds = $user->stores()->pluck('id');
        $unreadCount = Review::whereIn('store_id', $userStoreIds)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
            'review_id' => $review->id,
        ]);
    }
}
