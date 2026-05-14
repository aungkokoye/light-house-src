<?php

namespace Modules\Orders\Policies;

use App\Models\User;
use App\Policies\AppPolicy;
use Modules\Orders\Models\PaymentPrice;

class ProductPricePolicy extends OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->userPolicyCheck($user, OrderPolicy::LIST_ALLOW_PERMISSIONS)
            || $this->adminPolicyCheck($user, AppPolicy::LIST_ALLOW_PERMISSIONS);
    }

    public function delete(User $user, PaymentPrice $paymentPrice): bool
    {
        return $this->adminPolicyCheck($user, AppPolicy::DELETE_ALLOW_PERMISSIONS);
    }
}
