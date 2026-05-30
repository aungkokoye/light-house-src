<?php

namespace Modules\Orders\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Orders\Models\JobService;

class JobServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => 'Offset Printing',    'description' => 'High-volume offset printing for brochures, flyers, and books.'],
            ['name' => 'Digital Printing',   'description' => 'Short-run digital printing with fast turnaround and vibrant colour.'],
            ['name' => 'Large Format Print', 'description' => 'Wide-format printing for banners, posters, and signage.'],
            ['name' => 'Lamination',         'description' => 'Gloss, matte, or soft-touch lamination for print finishing.'],
            ['name' => 'Die Cutting',        'description' => 'Custom die-cut shapes for stickers, packaging, and cards.'],
        ];

        foreach ($services as $data) {
            JobService::create([...$data, 'created_by' => 1]);
        }
    }
}
