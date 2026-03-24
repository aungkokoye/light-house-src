<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserManager
{
    public function create(string $name, string $email, string $password, string $role): User
    {
        $user = User::create([
            'name'     => $name,
            'email'    => $email,
            'password' => Hash::make($password),
        ]);

        $user->assignRole($role);

        return $user;
    }

    public function delete(User $user): void
    {
        $user->syncRoles([]);
        $user->delete();
    }

    public function availableRoles(): array
    {
        return Role::pluck('name')->toArray();
    }
}