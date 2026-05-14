<?php

namespace Modules\Orders\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Orders\Models\Invoice;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            'customer_id' => null,
            'discount'    => fake()->randomElement([0, 0, 0, 5000, 10000, 20000, 50000]),
            'total'       => 0,
            'note'        => fake()->optional(0.4)->sentence(),
            'created_by'  => 1,
        ];
    }
}
