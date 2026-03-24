<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\UserManager;
use Illuminate\Console\Command;

/**
 * php artisan app:user-delete <email>
 */
class UserDelete extends Command
{
    protected $signature = 'app:user-delete {email : The email of the user to delete}';

    protected $description = 'Delete a user and remove all their roles';

    public function handle(UserManager $manager): int
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("No user found with email: {$email}");
            return self::FAILURE;
        }

        $this->table(['ID', 'Name', 'Email', 'Roles'], [
            [$user->id, $user->name, $user->email, $user->getRoleNames()->join(', ')],
        ]);

        if (! $this->confirm('Are you sure you want to delete this user?')) {
            $this->info('Cancelled.');
            return self::SUCCESS;
        }

        try {
            $manager->delete($user);
        } catch (\Throwable $e) {
            $this->error('Failed to delete user: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->info("User {$email} has been deleted and all roles removed.");

        return self::SUCCESS;
    }
}
