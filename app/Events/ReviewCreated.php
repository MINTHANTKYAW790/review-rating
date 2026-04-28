<?php

namespace App\Events;

use App\Models\Review;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReviewCreated implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Review $review,
        public int $ownerUserId
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->ownerUserId . '.reviews'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'review.created';
    }

    public function broadcastWith(): array
    {
        return [
            'review_id' => $this->review->id,
            'store_id' => $this->review->store_id,
            'created_at' => optional($this->review->created_at)->toISOString(),
        ];
    }
}
