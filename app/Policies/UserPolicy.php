<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy extends AppPolicy
{
    /** List — admin role only (no extra permission required). */
    public function viewAny(User $authUser): bool
    {
        return $this->basePolicyCheck($authUser, AppPolicy::LIST_ALLOW_PERMISSIONS);
    }

    /** View a single user — admin. */
    public function view(User $authUser, User $user): bool
    {
        return $this->basePolicyCheck($authUser, AppPolicy::VIEW_ALLOW_PERMISSIONS);
    }

    /** Create a new user — admin + (create). */
    public function create(User $authUser): bool
    {
        return $this->basePolicyCheck($authUser, AppPolicy::CREATE_ALLOW_PERMISSIONS);
    }

    /** Update a user — admin + (edit). */
    public function update(User $authUser, User $user): bool
    {
        return $this->basePolicyCheck($authUser, AppPolicy::UPDATE_ALLOW_PERMISSIONS);
    }

    /** Upload staff photo — admin + (create ). */
    public function uploadPhoto(User $authUser, User $user): bool
    {
        return $this->basePolicyCheck($authUser, AppPolicy::CREATE_ALLOW_PERMISSIONS);
    }

    /** Delete a user — admin + (super). */
    public function delete(User $authUser, User $user): bool
    {
        return $this->superUserPolicyCheck($authUser);
    }
}
