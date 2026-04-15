<?php

namespace App\Policies;

use App\Models\Site;
use App\Models\User;

class SitePolicy
{
    /** Super permission bypasses all checks. */
    public function before(User $authUser, string $ability): ?bool
    {
        return $authUser->hasPermissionTo('super') ? true : null;
    }

    /** List — any admin. */
    public function viewAny(User $authUser): bool
    {
        return $authUser->hasRole('admin');
    }

    /** View a single site — admin + view. */
    public function view(User $authUser, Site $site): bool
    {
        return $authUser->hasRole('admin') && $authUser->hasPermissionTo('view');
    }

    /** Create a site — admin + create. */
    public function create(User $authUser): bool
    {
        return $authUser->hasRole('admin') && $authUser->hasPermissionTo('create');
    }

    /** Update a site — admin + edit. */
    public function update(User $authUser, Site $site): bool
    {
        return $authUser->hasRole('admin') && $authUser->hasPermissionTo('edit');
    }

    /** Delete a site — admin + delete. */
    public function delete(User $authUser, Site $site): bool
    {
        return $authUser->hasRole('admin') && $authUser->hasPermissionTo('delete');
    }
}
