<?php

namespace App\Policies;

use App\Models\Store;
use App\Models\User;

class StorePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Store $store): bool
    {
        return $user->canAccessStore($store);
    }

    public function create(User $user): bool
    {
        return $user->can('create_stores') && $user->canOpenAnotherStore();
    }

    public function update(User $user, Store $store): bool
    {
        return (int) $store->user_id === (int) $user->id;
    }

    public function delete(User $user, Store $store): bool
    {
        return (int) $store->user_id === (int) $user->id;
    }

    public function restore(User $user, Store $store): bool
    {
        return false;
    }

    public function forceDelete(User $user, Store $store): bool
    {
        return false;
    }
}
