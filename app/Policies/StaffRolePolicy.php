<?php

namespace App\Policies;

use App\Models\StaffRole;
use App\Models\User;

class StaffRolePolicy
{
    /** Super permission bypasses all checks. */
    public function before(User $authUser, string $ability): ?bool
    {
        return $authUser->hasPermissionTo('super') ? true : null;
    }

    /** List / view — admin + edit. */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasRole('admin') && $authUser->hasPermissionTo('edit');
    }

    public function view(User $authUser, StaffRole $staffRole): bool
    {
        return $authUser->hasRole('admin') && $authUser->hasPermissionTo('edit');
    }

    /** Create / update — admin + edit. */
    public function create(User $authUser): bool
    {
        return $authUser->hasRole('admin') && $authUser->hasPermissionTo('edit');
    }

    public function update(User $authUser, StaffRole $staffRole): bool
    {
        return $authUser->hasRole('admin') && $authUser->hasPermissionTo('edit');
    }

    /** Delete — admin + delete. */
    public function delete(User $authUser, StaffRole $staffRole): bool
    {
        return $authUser->hasRole('admin') && $authUser->hasPermissionTo('delete');
    }
}
