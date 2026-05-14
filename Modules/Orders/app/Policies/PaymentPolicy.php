<?php

namespace Modules\Orders\Policies;

use App\Models\User;
use App\Policies\AppPolicy;
use Modules\Orders\Models\Payment;

class PaymentPolicy extends OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->superUserPolicyCheck($user)
            || $this->userPolicyCheck($user, OrderPolicy::LIST_ALLOW_PERMISSIONS);
    }

    public function view(User $user, Payment $payment): bool
    {
        return $this->superUserPolicyCheck($user)
            || $this->userPolicyCheck($user, OrderPolicy::CREATE_ALLOW_PERMISSIONS);
    }

    public function create(User $user): bool
    {
        return $this->superUserPolicyCheck($user)
            || $this->userPolicyCheck($user, OrderPolicy::CREATE_ALLOW_PERMISSIONS);
    }

    public function update(User $user, Payment $payment): bool
    {
        return $this->adminPolicyCheck($user, AppPolicy::UPDATE_ALLOW_PERMISSIONS) ||
            $this->userPolicyCheck($user, OrderPolicy::UPDATE_ALLOW_PERMISSIONS);
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $this->adminPolicyCheck($user, AppPolicy::DELETE_ALLOW_PERMISSIONS);
    }
}
