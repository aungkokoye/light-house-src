<?php

namespace Modules\Orders\Database\Seeders;

use Illuminate\Database\Seeder;

class OrdersDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            PaymentTypeSeeder::class,
            JobServiceSeeder::class,
            ProductSeeder::class,
            CustomerSeeder::class,
            InvoiceSeeder::class,
        ]);
    }
}
