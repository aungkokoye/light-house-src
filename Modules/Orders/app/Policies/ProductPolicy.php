<?php

namespace Modules\Orders\Policies;

use App\Models\User;
use App\Policies\AppPolicy;
use Modules\Orders\Models\Product;

class ProductPolicy extends OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->userPolicyCheck($user, OrderPolicy::LIST_ALLOW_PERMISSIONS)
            || $this->adminPolicyCheck($user, AppPolicy::LIST_ALLOW_PERMISSIONS);
    }

    public function view(User $user, Product $product): bool
    {
        return $this->userPolicyCheck($user, OrderPolicy::VIEW_ALLOW_PERMISSIONS)
            || $this->adminPolicyCheck($user, AppPolicy::VIEW_ALLOW_PERMISSIONS);
    }

    public function create(User $user): bool
    {
        return $this->adminPolicyCheck($user, AppPolicy::CREATE_ALLOW_PERMISSIONS);
    }

    public function update(User $user, Product $product): bool
    {
        return $this->adminPolicyCheck($user, AppPolicy::UPDATE_ALLOW_PERMISSIONS);
    }

    public function delete(User $user, Product $product): bool
    {
        return $this->adminPolicyCheck($user, AppPolicy::DELETE_ALLOW_PERMISSIONS);
    }
}

