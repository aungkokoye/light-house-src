<?php

namespace Modules\Orders\Policies;

use App\Models\User;
use Modules\Orders\Models\Invoice;

class InvoicePolicy extends OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->superUserPolicyCheck($user)
            || $this->userPolicyCheck($user, OrderPolicy::LIST_ALLOW_PERMISSIONS);
    }

    public function view(User $user, Invoice $invoice): bool
    {
        return $this->superUserPolicyCheck($user)
            || $this->userPolicyCheck($user, OrderPolicy::VIEW_ALLOW_PERMISSIONS);
    }

    public function create(User $user): bool
    {
        return $this->superUserPolicyCheck($user)
            || $this->userPolicyCheck($user, OrderPolicy::CREATE_ALLOW_PERMISSIONS);
    }

    public function update(User $user, Invoice $invoice): bool
    {
        return $this->superUserPolicyCheck($user)
            || $this->userPolicyCheck($user, OrderPolicy::UPDATE_ALLOW_PERMISSIONS);
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        return $this->superUserPolicyCheck($user)
            || $this->userPolicyCheck($user, OrderPolicy::DELETE_ALLOW_PERMISSIONS);
    }
}
