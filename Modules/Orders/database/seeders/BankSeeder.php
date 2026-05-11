<?php

namespace Modules\Orders\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Orders\Models\Bank;

class BankSeeder extends Seeder
{
    public function run(): void
    {
        $banks = ['KBZ Bank', 'AYA Bank', 'CB Bank', 'Yoma Bank'];

        foreach ($banks as $name) {
            Bank::firstOrCreate(['name' => $name], ['created_by' => 1]);
        }
    }
}
