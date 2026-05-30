<?php

namespace Modules\Orders\Policies;

use App\Models\User;
use App\Policies\AppPolicy;
use Modules\Orders\Models\Customer;

class CustomerPolicy extends OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->userPolicyCheck($user, OrderPolicy::LIST_ALLOW_PERMISSIONS)
            || $this->adminPolicyCheck($user, AppPolicy::LIST_ALLOW_PERMISSIONS);
    }

    public function view(User $user, Customer $customer): bool
    {
        return $this->userPolicyCheck($user, OrderPolicy::VIEW_ALLOW_PERMISSIONS)
            || $this->adminPolicyCheck($user, AppPolicy::VIEW_ALLOW_PERMISSIONS);
    }

    public function create(User $user): bool
    {
        return $this->userPolicyCheck($user, OrderPolicy::CREATE_ALLOW_PERMISSIONS)
            || $this->adminPolicyCheck($user, AppPolicy::CREATE_ALLOW_PERMISSIONS);
    }

    public function update(User $user, Customer $customer): bool
    {
        return $this->userPolicyCheck($user, OrderPolicy::UPDATE_ALLOW_PERMISSIONS)
            || $this->adminPolicyCheck($user, AppPolicy::UPDATE_ALLOW_PERMISSIONS);
    }

    public function delete(User $user, Customer $customer): bool
    {
        return $this->userPolicyCheck($user, OrderPolicy::DELETE_ALLOW_PERMISSIONS)
            || $this->adminPolicyCheck($user, AppPolicy::DELETE_ALLOW_PERMISSIONS);
    }
}
