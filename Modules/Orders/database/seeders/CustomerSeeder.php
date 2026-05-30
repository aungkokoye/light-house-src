<?php

namespace Modules\Orders\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Orders\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            'Myanmar Print Co.', 'Golden Star Media', 'Apex Publishing', 'Shwe Packaging Ltd.',
            'Delta Advertising', 'Lotus Stationery', 'Sunrise Events Co.', 'NWG Printers',
            'Royal Design Studio', 'Jade Mountain Trading', 'Ever Green Packaging',
            'Diamond Press House', 'Creative Minds Agency', 'Pearl Offset Ltd.',
            'Horizon Graphics', 'Andaman Media Group', 'Sky Blue Publishing',
        ];

        $titles = ['Director', 'Manager', 'CEO', 'Owner', 'GM', 'Procurement', 'Art Director',
                   'Designer', 'Sales Lead', 'Coordinator', 'Operations', 'Account Mgr'];

        for ($i = 0; $i < 25; $i++) {
            $hasCompany = fake()->boolean(75);
            $hasEmail   = fake()->boolean(70);

            Customer::create([
                'name'         => fake()->name(),
                'email'        => $hasEmail ? fake()->unique()->safeEmail() : null,
                'company_name' => $hasCompany ? fake()->randomElement($companies) : null,
                'title'        => $hasCompany ? fake()->randomElement($titles) : null,
                'phone'        => '+95 9 ' . fake()->numerify('### ### ###'),
                'address'      => fake()->address(),
                'created_by'   => 1,
            ]);
        }
    }
}
