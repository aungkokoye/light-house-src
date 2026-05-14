<?php

namespace Modules\Orders\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Orders\Models\InvoiceJob;

class InvoiceJobFactory extends Factory
{
    protected $model = InvoiceJob::class;

    public function definition(): array
    {
        $quantity  = fake()->numberBetween(1, 20);
        $unitPrice = fake()->randomElement([5000, 10000, 15000, 20000, 30000, 50000, 80000, 100000, 150000, 200000]);

        return [
            'invoice_id'    => null,
            'service_id'    => null,
            'product_id'    => null,
            'quantity'      => $quantity,
            'unit_price'    => $unitPrice,
            'total'         => $quantity * $unitPrice,
            'delivery_date' => fake()->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
            'created_by'    => 1,
        ];
    }
}
