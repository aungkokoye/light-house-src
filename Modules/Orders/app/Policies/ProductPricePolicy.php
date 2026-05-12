<?php

namespace Modules\Orders\Policies;

use App\Models\User;
use Modules\Orders\Models\PaymentPrice;

class ProductPricePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasPermissionTo('super') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'sale']);
    }

    public function delete(User $user, PaymentPrice $price): bool
    {
        return $user->hasRole('admin') && $user->hasPermissionTo('delete');
    }
}
