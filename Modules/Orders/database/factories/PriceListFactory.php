<?php

namespace Modules\Orders\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Orders\Models\JobService;
use Modules\Orders\Models\PriceList;

class PriceListFactory extends Factory
{
    protected $model = PriceList::class;

    public function definition(): array
    {
        $w = rand(50, 300);
        $h = rand(50, 300);

        return [
            'job_service_id'      => JobService::factory(),
            'product_description' => $this->faker->sentence(rand(4, 8)),
            'dimension'           => $this->faker->optional(0.8)->passthrough("{$w}×{$h}mm"),
            'price'               => $this->faker->randomElement([500, 1000, 2000, 3500, 5000, 8000, 10000, 15000, 20000, 35000, 50000, 80000, 100000]),
            'remark'              => $this->faker->optional(0.6)->sentence(),
            'created_by'          => 1,
        ];
    }
}
