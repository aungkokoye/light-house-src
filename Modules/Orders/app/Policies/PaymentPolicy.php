<?php

namespace Modules\Orders\Policies;

use App\Models\User;
use Modules\Orders\Models\Payment;

class PaymentPolicy
{
    /** Super permission bypasses all checks. */
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasPermissionTo('super') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'sale']);
    }

    public function view(User $user, Payment $payment): bool
    {
        return $user->hasRole(['admin', 'sale']) && $user->hasPermissionTo('view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'sale']) && $user->hasPermissionTo('create');
    }

    public function update(User $user, Payment $payment): bool
    {
        return $user->hasRole(['admin', 'sale']) && $user->hasPermissionTo('edit');
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $user->hasRole('admin') && $user->hasPermissionTo('delete');
    }
}
