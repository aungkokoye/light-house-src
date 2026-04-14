<?php

namespace App\Policies;

use App\Models\User;

class PermissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin')
            && $user->hasPermissionTo('super');
    }

    public function update(User $user): bool
    {
        return $user->hasRole('admin')
            && $user->hasPermissionTo('super');
    }

    public function delete(User $user): bool
    {
        return $user->hasRole('admin')
            && $user->hasPermissionTo('super');
    }
}