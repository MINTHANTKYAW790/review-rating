<?php

namespace App\Policies;

use App\Models\Store;
use App\Models\StoreNote;
use App\Models\User;

class StoreNotePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->accessibleStores()->exists();
    }

    public function view(User $user, StoreNote $storeNote): bool
    {
        return $user->accessibleStores()->whereKey($storeNote->store_id)->exists();
    }

    public function create(User $user, ?Store $store = null): bool
    {
        if ($store) {
            return $user->canAccessStore($store);
        }

        return $user->accessibleStores()->exists();
    }

    public function update(User $user, StoreNote $storeNote): bool
    {
        return $user->accessibleStores()->whereKey($storeNote->store_id)->exists();
    }

    public function delete(User $user, StoreNote $storeNote): bool
    {
        return $user->accessibleStores()->whereKey($storeNote->store_id)->exists();
    }
}
