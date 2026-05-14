<?php

namespace App\Policies;

use App\Models\StaffPosition;
use App\Models\User;

class StaffPositionPolicy extends AppPolicy
{

    /** List — any admin. */
    public function viewAny(User $authUser): bool
    {
        return $this->userPolicyCheck($authUser, AppPolicy::LIST_ALLOW_PERMISSIONS);
    }

    /** View a single position — admin + view. */
    public function view(User $authUser, StaffPosition $staffPosition): bool
    {
        return $this->userPolicyCheck($authUser, AppPolicy::VIEW_ALLOW_PERMISSIONS);
    }

    /** Create a position — admin + create. */
    public function create(User $authUser): bool
    {
        return $this->userPolicyCheck($authUser, AppPolicy::CREATE_ALLOW_PERMISSIONS);
    }

    /** Update a position — admin + edit. */
    public function update(User $authUser, StaffPosition $staffPosition): bool
    {
        return $this->userPolicyCheck($authUser, AppPolicy::UPDATE_ALLOW_PERMISSIONS);
    }

    /** Delete a position — admin + delete. */
    public function delete(User $authUser, StaffPosition $staffPosition): bool
    {
        return $this->userPolicyCheck($authUser, AppPolicy::DELETE_ALLOW_PERMISSIONS);
    }
}
