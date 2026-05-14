<?php

namespace App\Policies;

use App\Models\User;

class AppPolicy
{

    const array LIST_ALLOW_PERMISSIONS = ['list', 'view', 'create', 'update', 'delete', 'super'];
    const array VIEW_ALLOW_PERMISSIONS = ['view', 'create', 'update', 'delete', 'super'];
    const array CREATE_ALLOW_PERMISSIONS = ['create', 'update', 'delete', 'super'];
    const array UPDATE_ALLOW_PERMISSIONS = ['update', 'delete', 'super'];
    const array DELETE_ALLOW_PERMISSIONS = ['delete', 'super'];

    protected function userPolicyCheck(User $user, array $policies): bool
    {
        return $this->adminPolicyCheck($user, $policies);
    }

    protected function adminPolicyCheck(User $user, array $policies): bool
    {
        return $user->hasRole('admin') && $user->hasAnyPermission($policies);
    }

    protected function superUserPolicyCheck(User $user): bool
    {
        return $user->hasRole('admin')  && $user->hasPermissionTo('super');
    }

}
