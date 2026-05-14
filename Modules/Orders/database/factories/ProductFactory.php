<?php

namespace Modules\Orders\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Orders\Models\Product;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name'        => $this->faker->unique()->words(rand(2, 4), true),
            'description' => $this->faker->optional(0.7)->sentence(),
            'created_by'  => 1,
        ];
    }
}
