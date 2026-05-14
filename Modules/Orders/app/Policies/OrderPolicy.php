<?php

namespace Modules\Orders\Policies;

use App\Models\User;

class OrderPolicy
{

    const array LIST_ALLOW_PERMISSIONS = ['order_list', 'order_view', 'order_create', 'order_update', 'order_delete', 'super'];
    const array VIEW_ALLOW_PERMISSIONS = ['order_view', 'order_create', 'order_update', 'order_delete', 'super'];
    const array CREATE_ALLOW_PERMISSIONS = ['order_create', 'order_update', 'order_delete', 'super'];
    const array UPDATE_ALLOW_PERMISSIONS = ['order_update', 'order_delete', 'super'];
    const array DELETE_ALLOW_PERMISSIONS = ['order_delete', 'super'];

    protected function userPolicyCheck(User $user, array $userPolicies): bool
    {
        return $user->hasRole(['user']) && $user->hasAnyPermission($userPolicies);
    }

    protected function adminPolicyCheck(User $user, array $policies): bool
    {
        return $user->hasRole('admin') && $user->hasAnyPermission($policies);
    }
}
