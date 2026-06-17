<?php

namespace Modules\Orders\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Orders\Models\JobService;
use Modules\Orders\Models\PriceList;

class PriceListSeeder extends Seeder
{
    public function run(): void
    {
        $serviceIds = JobService::pluck('id')->all();

        PriceList::factory()
            ->count(30)
            ->sequence(fn ($seq) => [
                'job_service_id' => $serviceIds[$seq->index % count($serviceIds)],
            ])
            ->create(['created_by' => 1]);
    }
}
