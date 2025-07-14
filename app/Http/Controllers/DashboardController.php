<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Staff;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard1', [
            'storeRating' => Review::avg('rating'), // Assuming 1-5 scale
            'totalReviews' => Review::count(),
            'newReviews' => Review::where('created_at', '>=', now()->subWeek())->count(),
            'topStaff' => Staff::withAvg('reviews', 'rating')->orderByDesc('reviews_avg_rating')->first(),
        ]);
    }
}
