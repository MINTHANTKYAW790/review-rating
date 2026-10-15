<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Staff;
use App\Models\Store;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth('web')->user();
        $stores = $user->accessibleStores()->get();
        $storeIds = $stores->pluck('id');

        $totalReviews = 0;
        $lastWeekCount = 0;
        $prevWeekCount = 0;
        $reviewGrowth = 0;
        $topStaff = null;
        $topStore = null;
        $ratingCharts = collect();

        if ($stores->isNotEmpty()) {
            $reviews = Review::query()->whereIn('store_id', $storeIds)->get();

            $totalReviews = $reviews->count();
            $lastWeekCount = $reviews->where('created_at', '>=', now()->subWeek())->count();
            $prevWeekCount = $reviews
                ->whereBetween('created_at', [now()->subWeeks(2), now()->subWeek()])
                ->count();
            $reviewGrowth = $prevWeekCount > 0
                ? number_format(($lastWeekCount - $prevWeekCount) / $prevWeekCount * 100, 2)
                : 100;

            $topStaff = Staff::withAvg('reviews', 'rating_staff')
                ->whereIn('store_id', $storeIds)
                ->orderByDesc('reviews_avg_rating_staff')
                ->first();

            $topStore = Store::withAvg('reviews', 'rating_store')
                ->whereIn('id', $storeIds)
                ->orderByDesc('reviews_avg_rating_store')
                ->first();

            $countsByStore = Review::query()
                ->whereIn('store_id', $storeIds)
                ->selectRaw('store_id, rating_store, COUNT(*) as aggregate')
                ->groupBy('store_id', 'rating_store')
                ->get()
                ->groupBy(fn ($row) => (int) $row->store_id);

            $ratingCharts = $stores->map(function (Store $store) use ($countsByStore) {
                $byStar = $countsByStore->get($store->id, collect())->mapWithKeys(
                    fn ($row) => [(int) $row->rating_store => $row]
                );
                $counts = [];
                for ($star = 5; $star >= 1; $star--) {
                    $counts[] = (int) ($byStar->get($star)?->aggregate ?? 0);
                }

                return [
                    'storeId' => $store->id,
                    'storeName' => $store->name,
                    'counts' => $counts,
                    'total' => array_sum($counts),
                ];
            })->values();
        }

        $starLabels = collect([5, 4, 3, 2, 1])
            ->map(fn (int $count) => __('messages.star_count', ['count' => $count]))
            ->all();

        return view('dashboard', [
            'totalReviews' => $totalReviews,
            'newReviewsThisWeek' => $lastWeekCount,
            'newReviewIncrease' => $lastWeekCount - $prevWeekCount,
            'reviewGrowth' => $reviewGrowth,
            'topStaff' => $topStaff,
            'store' => $topStore,
            'hasStores' => $stores->isNotEmpty(),
            'isManager' => $user->getRoleNames()->contains('manager') && ! $user->getRoleNames()->contains('admin'),
            'ratingCharts' => $ratingCharts,
            'starLabels' => $starLabels,
        ]);
    }
}
