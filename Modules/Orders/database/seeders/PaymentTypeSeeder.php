<?php

namespace Modules\Orders\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Orders\Models\PaymentType;

class PaymentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $paymentTypes = ['Cash', 'KBZ Bank', 'AYA Bank', 'CB Bank', 'Yoma Bank'];

        foreach ($paymentTypes as $name) {
            PaymentType::firstOrCreate(['name' => $name], ['created_by' => 1]);
        }
    }
}
