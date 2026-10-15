<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Review $review): bool
    {
        return $user->accessibleStores()->whereKey($review->store_id)->exists();
    }

    public function delete(User $user, Review $review): bool
    {
        return $user->stores()->whereKey($review->store_id)->exists();
    }

    public function update(User $user, Review $review): bool
    {
        return $user->accessibleStores()->whereKey($review->store_id)->exists();
    }
}
