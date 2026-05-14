<?php

namespace App\Policies;

use App\Models\User;

class ChatPolicy extends AppPolicy
{
    public function stream(User $user): bool
    {
        return config('ai.enabled') && $this->superUserPolicyCheck($user);
    }
}
