<?php

namespace Modules\Orders\Policies;

use App\Models\User;
use Modules\Orders\Models\Invoice;

class InvoicePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        return $user->hasPermissionTo('super') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'sale']);
    }

    public function view(User $user, Invoice $invoice): bool
    {
        return $user->hasRole(['admin', 'sale']) && $user->hasPermissionTo('view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'sale']) && $user->hasPermissionTo('create');
    }

    public function update(User $user, Invoice $invoice): bool
    {
        return $user->hasRole(['admin', 'sale']) && $user->hasPermissionTo('edit');
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        return $user->hasRole('admin') && $user->hasPermissionTo('delete');
    }
}
