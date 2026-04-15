<?php

namespace App\Policies;

use App\Models\StaffPosition;
use App\Models\User;

class StaffPositionPolicy
{
    /** Super permission bypasses all checks. */
    public function before(User $authUser, string $ability): ?bool
    {
        return $authUser->hasPermissionTo('super') ? true : null;
    }

    /** List — any admin. */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasRole('admin');
    }

    /** View a single position — admin + view. */
    public function view(User $authUser, StaffPosition $staffPosition): bool
    {
        return $authUser->hasRole('admin') && $authUser->hasPermissionTo('view');
    }

    /** Create a position — admin + create. */
    public function create(User $authUser): bool
    {
        return $authUser->hasRole('admin') && $authUser->hasPermissionTo('create');
    }

    /** Update a position — admin + edit. */
    public function update(User $authUser, StaffPosition $staffPosition): bool
    {
        return $authUser->hasRole('admin') && $authUser->hasPermissionTo('edit');
    }

    /** Delete a position — admin + delete. */
    public function delete(User $authUser, StaffPosition $staffPosition): bool
    {
        return $authUser->hasRole('admin') && $authUser->hasPermissionTo('delete');
    }
}
