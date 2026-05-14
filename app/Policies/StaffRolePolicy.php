<?php

namespace App\Policies;

use App\Models\StaffRole;
use App\Models\User;

class StaffRolePolicy extends AppPolicy
{
    /** List / view — admin + edit. */
    public function viewAny(User $authUser): bool
    {
        return $this->userPolicyCheck($authUser, AppPolicy::UPDATE_ALLOW_PERMISSIONS);
    }

    public function view(User $authUser, StaffRole $staffRole): bool
    {
        return $this->userPolicyCheck($authUser, AppPolicy::UPDATE_ALLOW_PERMISSIONS);
    }

    /** Create / update — admin + edit. */
    public function create(User $authUser): bool
    {
        return $this->userPolicyCheck($authUser, AppPolicy::UPDATE_ALLOW_PERMISSIONS);
    }

    public function update(User $authUser, StaffRole $staffRole): bool
    {
        return $this->userPolicyCheck($authUser, AppPolicy::UPDATE_ALLOW_PERMISSIONS);
    }

    /** Delete — admin + delete. */
    public function delete(User $authUser, StaffRole $staffRole): bool
    {
        return $this->userPolicyCheck($authUser, AppPolicy::UPDATE_ALLOW_PERMISSIONS);
    }
}
