<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Admin user
        $admin = User::factory()->create([
            'name'              => 'Admin User',
            'email'             => 'admin@lighthouse.com',
            'email_verified_at' => $now,
            'password'          => Hash::make('Password!'),
        ]);
        $admin->assignRole('admin');

        // Normal users
        User::factory(50)->create([
            'email_verified_at' => $now,
        ])->each->assignRole('user');
    }
}
