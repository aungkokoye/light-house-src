<?php

namespace Modules\Orders\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Orders\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::factory()
            ->count(15)
            ->create()
            ->each(function (Product $product) {
                $count = rand(1, 3);
                for ($i = 0; $i < $count; $i++) {
                    $product->prices()->create([
                        'per_price'  => rand(1000, 500000),
                        'created_by' => 1,
                    ]);
                }
            });
    }
}
