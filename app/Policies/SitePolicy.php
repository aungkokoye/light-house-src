<?php

namespace App\Policies;

use App\Models\Site;
use App\Models\User;

class SitePolicy extends AppPolicy
{
    /** List — any admin. */
    public function viewAny(User $authUser): bool
    {
        return $this->userPolicyCheck($authUser, AppPolicy::LIST_ALLOW_PERMISSIONS);
    }

    /** View a single site — admin + view. */
    public function view(User $authUser, Site $site): bool
    {
        return $this->userPolicyCheck($authUser, AppPolicy::VIEW_ALLOW_PERMISSIONS);
    }

    /** Create a site — admin + create. */
    public function create(User $authUser): bool
    {
        return $this->userPolicyCheck($authUser, AppPolicy::CREATE_ALLOW_PERMISSIONS);
    }

    /** Update a site — admin + edit. */
    public function update(User $authUser, Site $site): bool
    {
        return $this->userPolicyCheck($authUser, AppPolicy::UPDATE_ALLOW_PERMISSIONS);
    }

    /** Delete a site — admin + delete. */
    public function delete(User $authUser, Site $site): bool
    {
        return $this->userPolicyCheck($authUser, AppPolicy::DELETE_ALLOW_PERMISSIONS);
    }
}
