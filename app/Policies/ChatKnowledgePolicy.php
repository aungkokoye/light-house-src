<?php

namespace App\Policies;

use App\Models\User;

class ChatKnowledgePolicy extends AppPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->superUserPolicyCheck($user);
    }

    public function view(User $user): bool
    {
        return $this->superUserPolicyCheck($user);
    }

    public function create(User $user): bool
    {
        return $this->superUserPolicyCheck($user);
    }

    public function update(User $user): bool
    {
        return $this->superUserPolicyCheck($user);
    }

    public function delete(User $user): bool
    {
        return $this->superUserPolicyCheck($user);
    }
}
