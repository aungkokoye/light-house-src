<?php

namespace App\Policies;

use App\Models\User;

class AuditLogPolicy extends AppPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->superUserPolicyCheck($user);
    }

    public function view(User $user): bool
    {
        return $this->superUserPolicyCheck($user);
    }
}
