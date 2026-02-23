<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Staff;
use App\Models\Store;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth('web')->user();
        $stores = $user->stores; // This is a Collection of stores

        $totalReviews = 0;
        $lastWeekCount = 0;
        $prevWeekCount = 0;
        $averageRating = 0;
        $reviewGrowth = 0;
        $topStaff = null;
        $topStore = null;

        if ($stores->isNotEmpty()) {
            // Aggregate total reviews across all stores
            $totalReviews = $stores->flatMap(fn ($store) => $store->reviews)->count();

            // Aggregate reviews for the last week
            $lastWeekCount = $stores->flatMap(fn ($store) => $store->reviews)
                                   ->where('created_at', '>=', now()->subWeek())
                                   ->count();

            // Aggregate reviews for the previous week
            $prevWeekCount = $stores->flatMap(fn ($store) => $store->reviews)
                                   ->whereBetween('created_at', [now()->subWeeks(2), now()->subWeek()])
                                   ->count();

            // Calculate average rating across all stores
            $averageRating = $stores->flatMap(fn ($store) => $store->reviews)->avg('rating_store');

            // Calculate review growth
            $reviewGrowth = $prevWeekCount > 0 ? number_format(($lastWeekCount - $prevWeekCount) / $prevWeekCount * 100, 2) : 100;

            // Find top staff across all user's stores
            $topStaff = Staff::withAvg('reviews', 'rating_staff')
                ->whereIn('store_id', $stores->pluck('id'))
                ->orderByDesc('reviews_avg_rating_staff')
                ->first();

            // Find top store among user's stores
            $topStore = Store::withAvg('reviews', 'rating_store')
                ->whereIn('id', $stores->pluck('id'))
                ->orderByDesc('reviews_avg_rating_store')
                ->first();
        }

        info('Top Store: ' . ($topStore ? $topStore->name : 'N/A')); // Debugging
        info('Total Reviews: ' . $totalReviews); // Debugging

        return view('dashboard', [
            'totalReviews' => $totalReviews,
            'newReviewsThisWeek' => $lastWeekCount,
            'newReviewIncrease' => $lastWeekCount - $prevWeekCount,
            'reviewGrowth' => $reviewGrowth,
            'topStaff' => $topStaff,
            'store' => $topStore, // Passing the top store, or null if no stores
            'hasStores' => $stores->isNotEmpty(), // Indicate if the user has any stores
        ]);
    }
}
