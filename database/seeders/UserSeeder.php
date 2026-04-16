<?php

namespace Database\Seeders;

use App\Models\CompanyProfile;
use App\Models\Site;
use App\Models\StaffPosition;
use App\Models\StaffProfile;
use App\Models\StaffRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // --- Sites ---
        $sites = collect([
            ['name' => 'Yangon Site',    'description' => 'Main printing site in Yangon.',    'address' => 'Yangon, Myanmar',    'phone' => '09-111-111'],
            ['name' => 'Mandalay Site',  'description' => 'Printing site in Mandalay.',       'address' => 'Mandalay, Myanmar',  'phone' => '09-222-222'],
            ['name' => 'Naypyidaw Site', 'description' => 'Printing site in Naypyidaw.',      'address' => 'Naypyidaw, Myanmar', 'phone' => '09-333-333'],
            ['name' => 'Bago Site',      'description' => 'Printing site in Bago.',           'address' => 'Bago, Myanmar',      'phone' => '09-444-444'],
        ])->map(fn ($s) => Site::create($s));

        // --- Staff positions ---
        $positions = collect([
            ['name' => 'Operator Manager',   'description' => 'Operates printing system application.'],
            ['name' => 'Press Operator',     'description' => 'Operates printing press machines.'],
            ['name' => 'Senior Operator',    'description' => 'Senior-level press machine operator.'],
            ['name' => 'Supervisor',         'description' => 'Oversees daily site operations.'],
            ['name' => 'Site Manager',       'description' => 'Manages overall site activities.'],
            ['name' => 'Technician',         'description' => 'Handles equipment maintenance and repairs.'],
            ['name' => 'Quality Controller', 'description' => 'Ensures print quality standards are met.'],
        ])->map(fn ($p) => StaffPosition::create($p));

        $operatorManagerId = $positions->firstWhere('name', 'Operator Manager')->id;

        // --- Admins (staff) ---
        $admin = $this->createAdmin(
            name:        'Admin User',
            email:       'admin@lighthouse.com',
            permissions: Permission::all(),
            positions:   $positions,
            sites:       $sites,
            positionId:  $operatorManagerId,
            now:         $now,
        );

        $this->createAdmin(
            name:        'Second Admin User',
            email:       'admin2@lighthouse.com',
            permissions: ['view', 'create', 'edit'],
            positions:   $positions,
            sites:       $sites,
            positionId:  $operatorManagerId,
            createdBy:   $admin->id,
            now:         $now,
        );

        // --- Customer users ---
        User::factory(10)->create(['email_verified_at' => $now])
            ->each(function (User $user) use ($admin) {
                $user->assignRole('customer');

                CompanyProfile::create([
                    'user_id'     => $user->id,
                    'name'        => fake()->company(),
                    'role'        => fake()->jobTitle(),
                    'description' => fake()->sentence(),
                    'address'     => fake()->address(),
                    'phone'       => fake()->phoneNumber(),
                    'created_by'  => $admin->id,
                ]);
            });

        // --- Staff users (role = user) ---
        User::factory(30)->create(['email_verified_at' => $now])
            ->each(function (User $user) use ($admin, $positions, $sites) {
                $user->assignRole('user');

                $staffProfile = StaffProfile::create([
                    'user_id'    => $user->id,
                    'full_name'  => $user->name,
                    'nrc_no'     => fake()->numerify('##/######(N)######'),
                    'dob'        => fake()->dateTimeBetween('-50 years', '-20 years')->format('Y-m-d'),
                    'address'    => fake()->address(),
                    'phone'      => fake()->phoneNumber(),
                    'created_by' => $admin->id,
                    'start_date' => fake()->dateTimeBetween('-5 years', '-6 months')->format('Y-m-d'),
                ]);

                $this->seedStaffRoles($staffProfile, $positions, $sites, $admin->id);
            });
    }

    private function createAdmin(
        string $name,
        string $email,
        mixed $permissions,
        $positions,
        $sites,
        int $positionId,
        ?int $createdBy = null,
        Carbon $now = new Carbon,
    ): User {
        $user = User::factory()->create([
            'name'              => $name,
            'email'             => $email,
            'email_verified_at' => $now,
            'password'          => Hash::make('Password!'),
        ]);

        $user->assignRole('admin');
        $user->syncPermissions($permissions);

        $profile = StaffProfile::create([
            'user_id'    => $user->id,
            'full_name'  => $user->name,
            'nrc_no'     => fake()->numerify('##/######(N)######'),
            'dob'        => fake()->dateTimeBetween('-50 years', '-25 years')->format('Y-m-d'),
            'address'    => fake()->address(),
            'phone'      => fake()->phoneNumber(),
            'created_by' => $createdBy ?? $user->id,
            'start_date' => fake()->dateTimeBetween('-5 years', '-1 year')->format('Y-m-d'),
        ]);

        $this->seedStaffRoles($profile, $positions, $sites, $user->id, $positionId);

        return $user;
    }

    private function seedStaffRoles(StaffProfile $profile, $positions, $sites, int $createdBy, ?int $fixedPositionId = null): void
    {
        $count     = fake()->numberBetween(1, 3);
        $roleStart = Carbon::parse($profile->start_date);

        for ($i = 0; $i < $count; $i++) {
            $isLast  = $i === $count - 1;
            $roleEnd = $isLast ? null : $roleStart->copy()->addMonths(fake()->numberBetween(3, 12));

            StaffRole::create([
                'staff_profile_id'  => $profile->id,
                'staff_position_id' => $fixedPositionId ?? $positions->random()->id,
                'salary'            => fake()->numberBetween(200_000, 1_500_000),
                'site_id'           => $sites->random()->id,
                'start_date'        => $roleStart->format('Y-m-d'),
                'end_date'          => $roleEnd?->format('Y-m-d'),
                'created_by'        => $createdBy,
            ]);

            if ($roleEnd) {
                $roleStart = $roleEnd->copy()->addDay();
            }
        }
    }
}
