<?php

namespace Modules\Orders\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Orders\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Business Card',          'description' => 'Standard and premium business cards, single or double-sided.'],
            ['name' => 'Flyer',                  'description' => 'Single-page promotional flyers in A4, A5, and A6 sizes.'],
            ['name' => 'Brochure',               'description' => 'Bi-fold and tri-fold brochures for marketing and events.'],
            ['name' => 'Poster',                 'description' => 'Large-format posters for indoor and outdoor display.'],
            ['name' => 'Banner',                 'description' => 'Roll-up, hanging, and vinyl banners for events and signage.'],
            ['name' => 'Letterhead',             'description' => 'Branded company letterhead on premium paper stock.'],
            ['name' => 'Envelope',               'description' => 'Custom-printed envelopes in DL, C5, and C4 formats.'],
            ['name' => 'Notebook',               'description' => 'Spiral-bound and perfect-bound custom notebooks.'],
            ['name' => 'Invoice Book',           'description' => 'Carbonless duplicate and triplicate invoice booklets.'],
            ['name' => 'Sticker',                'description' => 'Die-cut and sheet stickers on gloss or matte vinyl.'],
            ['name' => 'Calendar',               'description' => 'Wall and desk calendars with custom artwork per month.'],
            ['name' => 'Packaging Box',          'description' => 'Custom printed folding cartons and product boxes.'],
            ['name' => 'Certificate',            'description' => 'Full-colour certificates on heavy art card or textured paper.'],
            ['name' => 'ID Card',                'description' => 'PVC or paper-laminated ID cards with custom design.'],
            ['name' => 'Menu Card',              'description' => 'Restaurant and hotel menus in laminated or uncoated finish.'],
        ];

        foreach ($products as $data) {
            $product = Product::create([...$data, 'created_by' => 1]);

            $count = rand(1, 3);
            for ($i = 0; $i < $count; $i++) {
                $product->prices()->create([
                    'per_price'  => fake()->randomElement([5000, 8000, 10000, 15000, 20000, 25000, 30000, 50000, 80000, 100000]),
                    'created_by' => 1,
                ]);
            }
        }
    }
}
