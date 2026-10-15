<?php

namespace App\Policies;

use App\Models\Staff;
use App\Models\User;

class StaffPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Staff $staff): bool
    {
        return $user->accessibleStores()->whereKey($staff->store_id)->exists();
    }

    public function create(User $user): bool
    {
        return $user->can('create_staff');
    }

    public function update(User $user, Staff $staff): bool
    {
        return $user->can('edit_staff')
            && $user->stores()->whereKey($staff->store_id)->exists();
    }

    public function delete(User $user, Staff $staff): bool
    {
        return $user->can('delete_staff')
            && $user->stores()->whereKey($staff->store_id)->exists();
    }
}
