<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /** List — admin role only (no extra permission required). */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasRole('admin');
    }

    /** View a single user — admin + (view | super). */
    public function view(User $authUser, User $user): bool
    {
        return $authUser->hasRole('admin')
            && ($authUser->hasPermissionTo('view') || $authUser->hasPermissionTo('super'));
    }

    /** Create a new user — admin + (create | super). */
    public function create(User $authUser): bool
    {
        return $authUser->hasRole('admin')
            && ($authUser->hasPermissionTo('create') || $authUser->hasPermissionTo('super'));
    }

    /** Update a user — admin + (edit | super). */
    public function update(User $authUser, User $user): bool
    {
        return $authUser->hasRole('admin')
            && ($authUser->hasPermissionTo('edit') || $authUser->hasPermissionTo('super'));
    }

    /** Delete a user — admin + (delete | super). */
    public function delete(User $authUser, User $user): bool
    {
        return $authUser->hasRole('admin')
            && ($authUser->hasPermissionTo('delete') || $authUser->hasPermissionTo('super'));
    }
}