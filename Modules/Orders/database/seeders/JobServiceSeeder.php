<?php

namespace Modules\Orders\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Orders\Models\JobService;

class JobServiceSeeder extends Seeder
{
    public function run(): void
    {
        JobService::factory()->count(25)->create();
    }
}
