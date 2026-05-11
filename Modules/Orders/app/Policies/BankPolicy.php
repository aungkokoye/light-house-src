<?php

namespace Modules\Orders\Policies;

use App\Models\User;
use Modules\Orders\Models\Bank;

class BankPolicy
{
    /** Super permission bypasses all checks. */
    public function before(User $authUser, string $ability): ?bool
    {
        return $authUser->hasPermissionTo('super') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'sale']);
    }

    public function view(User $user, Bank $bank): bool
    {
        return $user->hasRole(['admin', 'sale']) && $user->hasPermissionTo('view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin') && $user->hasPermissionTo('create');
    }

    public function update(User $user, Bank $bank): bool
    {
        return $user->hasRole('admin') && $user->hasPermissionTo('edit');
    }

    public function delete(User $user, Bank $bank): bool
    {
        return $user->hasRole('admin') && $user->hasPermissionTo('delete');
    }
}
