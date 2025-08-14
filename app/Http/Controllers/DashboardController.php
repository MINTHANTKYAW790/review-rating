<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Staff;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $store = auth('web')->user()->store;

        $totalReviews = $store->reviews()->count();
        $lastWeekCount = $store->reviews()->where('created_at', '>=', now()->subWeek())->count();
        $prevWeekCount = $store->reviews()->whereBetween('created_at', [now()->subWeeks(2), now()->subWeek()])->count();

        $averageRating = $store->reviews()->avg('rating_store');
        $reviewGrowth = $prevWeekCount > 0 ? number_format(($lastWeekCount - $prevWeekCount) / $prevWeekCount * 100, 2) : 100;

        $topStaff = $store->staff()->orderByDesc('rating')->first();

        return view('dashboard', [
            'store' => $store->setAttribute('average_rating', number_format($averageRating, 1)),
            'totalReviews' => $totalReviews,
            'newReviewsThisWeek' => $lastWeekCount,
            'newReviewIncrease' => $lastWeekCount - $prevWeekCount,
            'reviewGrowth' => $reviewGrowth,
            'topStaff' => $topStaff,
        ]);
    }
}
