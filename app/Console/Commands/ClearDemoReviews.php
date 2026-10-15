<?php

namespace App\Console\Commands;

use App\Models\Review;
use App\Models\Store;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ClearDemoReviews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:clear-reviews';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purge all demo reviews and ratings daily at midnight';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $demoStoreIds = Store::query()
            ->whereHas('user', function ($q) {
                $q->where('email', 'demo@gmail.test');
            })
            ->orWhere('name', 'like', '%demo%')
            ->pluck('id');

        $reviews = Review::query()
            ->where('is_demo', true)
            ->orWhereIn('store_id', $demoStoreIds)
            ->get();

        $count = $reviews->count();

        foreach ($reviews as $review) {
            $review->delete();
        }

        $message = "Successfully deleted {$count} demo reviews and ratings.";
        $this->info($message);
        Log::info('[ClearDemoReviews] ' . $message);

        return self::SUCCESS;
    }
}
