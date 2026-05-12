<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /** Super permission bypasses all checks. */
    public function before(User $authUser, string $ability): ?bool
    {
        return $authUser->hasPermissionTo('super') ? true : null;
    }

    /** List — admin role only (no extra permission required). */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasRole('admin');
    }

    /** View a single user — admin. */
    public function view(User $authUser, User $user): bool
    {
        return $authUser->hasRole('admin') && $authUser->hasPermissionTo('view');
    }

    /** Create a new user — admin + (create). */
    public function create(User $authUser): bool
    {
        return $authUser->hasRole('admin') && $authUser->hasPermissionTo('create');
    }

    /** Update a user — admin + (edit). */
    public function update(User $authUser, User $user): bool
    {
        return $authUser->hasRole('admin') && $authUser->hasPermissionTo('edit');
    }

    /** Delete a user — admin + (delete | super). */
    public function delete(User $authUser, User $user): bool
    {
        return $authUser->hasRole('admin') && $authUser->hasPermissionTo('super');
    }
}
