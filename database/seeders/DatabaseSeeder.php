<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Orders\Database\Seeders\OrdersDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command->warn('Seeders are disabled in production.');
            return;
        }

        $this->call([
            UserSeeder::class,
            OrdersDatabaseSeeder::class,
        ]);
    }
}
