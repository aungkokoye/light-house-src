<?php

namespace App\Policies;

use App\Models\User;

class ChatPolicy
{
    public function stream(User $user): bool
    {
        return config('ai.enabled')
            && $user->hasRole('admin')
            && $user->hasPermissionTo('super');
    }
}
