<?php

namespace App\Console\Commands;

use App\Services\EmailManager;
use App\Services\UserManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

class UserCreate extends Command
{
    protected $signature = 'app:user-create';

    protected $description = 'Create a new user';

    public function handle(UserManager $manager, EmailManager $emailManager): int
    {
        $name = $this->ask('Name');
        if (Validator::make(['name' => $name], ['name' => 'required|string|max:255|unique:users'])->fails()) {
            $this->error('Name is required or already taken.');
            return self::FAILURE;
        }

        $email = $this->ask('Email');
        if (Validator::make(['email' => $email], ['email' => 'required|email|unique:users'])->fails()) {
            $this->error('Invalid or already taken email address.');
            return self::FAILURE;
        }

        $password = $this->secret('Password (min. 8 characters)');
        if (strlen($password) < 8) {
            $this->error('Password must be at least 8 characters.');
            return self::FAILURE;
        }

        $roles = $manager->availableRoles();
        if (empty($roles)) {
            $this->error('No roles found. Run db:seed --class=RoleSeeder first.');
            return self::FAILURE;
        }

        $role = $this->choice('Role', $roles, 0);

        $permissions = $role === 'admin' ? Permission::pluck('name')->toArray() : [];

        try {
            $user = $manager->create($name, $email, $password, $role, permissions: $permissions);
            $emailManager->sendVerificationEmail($user);
        } catch (\Throwable $e) {
            $this->error('Failed to create user or send verification email: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->info('User created successfully. A verification email has been sent.');
        $this->table(['ID', 'Name', 'Email', 'Role'], [
            [$user->id, $user->name, $user->email, $role],
        ]);

        return self::SUCCESS;
    }
}
