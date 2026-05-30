<?php

namespace Modules\Orders\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Orders\Models\Payment;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'invoice_id'   => null,
            'payment_type_id' => null,
            'stage'        => Payment::STAGE_ADVANCE,
            'amount'       => 0,
            'note'         => fake()->optional(0.4)->sentence(),
            'payment_date' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'created_by'   => 1,
        ];
    }
}
