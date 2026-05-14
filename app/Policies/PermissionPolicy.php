<?php

namespace App\Policies;

use App\Models\User;

class PermissionPolicy extends AppPolicy
{
    public function viewAny(User $user): bool
    {
        return  $this->basePolicyCheck($user, AppPolicy::LIST_ALLOW_PERMISSIONS);
    }

    public function view(User $user): bool
    {
        return  $this->basePolicyCheck($user, AppPolicy::VIEW_ALLOW_PERMISSIONS);
    }

    public function create(User $user): bool
    {
        return $this->superUserPolicyCheck($user);
    }

    public function update(User $user): bool
    {
        return $this->superUserPolicyCheck($user);
    }

    public function delete(User $user): bool
    {
        return $this->superUserPolicyCheck($user);
    }
}
