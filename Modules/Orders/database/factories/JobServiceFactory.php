<?php

namespace Modules\Orders\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Orders\Models\JobService;

class JobServiceFactory extends Factory
{
    protected $model = JobService::class;

    public function definition(): array
    {
        return [
            'name'        => $this->faker->unique()->words(rand(2, 3), true),
            'description' => $this->faker->optional(0.7)->sentence(),
            'created_by'  => 1,
        ];
    }
}
