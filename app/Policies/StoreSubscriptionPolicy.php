<?php

namespace App\Policies;

use App\Models\StoreSubscription;
use App\Models\User;

class StoreSubscriptionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('manage_subscriptions');
    }

    public function view(User $user, StoreSubscription $storeSubscription): bool
    {
        return $user->can('manage_subscriptions');
    }
}
