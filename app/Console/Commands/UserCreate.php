<?php

namespace App\Console\Commands;

use App\Models\Site;
use App\Models\StaffPosition;
use App\Services\CompanyProfileManager;
use App\Services\EmailManager;
use App\Services\StaffProfileManager;
use App\Services\UserManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

class UserCreate extends Command
{
    protected $signature = 'app:user-create';

    protected $description = 'Create a new user';

    public function handle(
        UserManager $manager,
        EmailManager $emailManager,
        CompanyProfileManager $companyProfileManager,
        StaffProfileManager $staffProfileManager,
    ): int {
        // ── Base user ─────────────────────────────────────────────────────────
        $name = $this->ask('Name');
        if (Validator::make(['name' => $name], ['name' => 'required|string|max:255|unique:users'])->fails()) {
            $this->error('Name is required or already taken.');
            return self::FAILURE;
        }

        $email = $this->ask('Email');
        if (Validator::make(['email' => $email], ['email' => 'required|email|unique:users'])->fails()) {
            $this->error('Invalid or already taken email address.');
            return self::FAILURE;
        }

        $password = $this->secret('Password (min. 8 characters)');
        if (strlen($password) < 8) {
            $this->error('Password must be at least 8 characters.');
            return self::FAILURE;
        }

        // ── Role ──────────────────────────────────────────────────────────────
        $roles = $manager->availableRoles();
        if (empty($roles)) {
            $this->error('No roles found. Run db:seed --class=RoleSeeder first.');
            return self::FAILURE;
        }

        $role = $this->choice('Role', $roles, 0);

        // ── Permissions ───────────────────────────────────────────────────────
        $permissions = $role === 'admin' ? Permission::pluck('name')->toArray() : [];

        if ($role !== 'admin') {
            $allPermissions = Permission::pluck('name')->toArray();

            if (! empty($allPermissions) && $this->confirm('Assign specific permissions?', false)) {
                $selected = [];
                foreach ($allPermissions as $perm) {
                    if ($this->confirm("  Grant [{$perm}]?", false)) {
                        $selected[] = $perm;
                    }
                }
                $permissions = $selected;
            }
        }

        // ── Create user + profile in a single transaction ─────────────────────
        try {
            $user = DB::transaction(function () use ($manager, $companyProfileManager, $staffProfileManager, $name, $email, $password, $role, $permissions) {
                $user = $manager->create($name, $email, $password, $role, permissions: $permissions);

                if ($role === 'customer') {
                    $companyProfileManager->create($user, $this->askCompanyProfile());
                } else {
                    [$profileData, $roleData] = $this->askStaffProfile();
                    $staffProfileManager->create($user, $profileData, $roleData);
                }

                return $user;
            });
        } catch (\Throwable $e) {
            $this->error('Failed to create user: ' . $e->getMessage());
            return self::FAILURE;
        }

        // ── Send verification email (outside transaction) ─────────────────────
        try {
            $emailManager->sendVerificationEmail($user);
        } catch (\Throwable $e) {
            $this->warn('User created but verification email failed: ' . $e->getMessage());
        }

        $this->info('User created successfully. A verification email has been sent.');
        $this->table(['ID', 'Name', 'Email', 'Role'], [
            [$user->id, $user->name, $user->email, $role],
        ]);

        return self::SUCCESS;
    }

    // ── Prompts ───────────────────────────────────────────────────────────────

    private function askCompanyProfile(): array
    {
        $this->info('── Company Profile ──────────────────────────────');

        return [
            'name'        => $this->askRequired('Company name'),
            'role'        => $this->askRequired('Role / title at the company'),
            'address'     => $this->askRequired('Address'),
            'phone'       => $this->askRequired('Phone'),
            'description' => $this->ask('Description (optional)', null),
        ];
    }

    private function askStaffProfile(): array
    {
        $this->info('── Staff Profile ────────────────────────────────');

        $profileData = [
            'full_name'               => $this->askRequired('Full name'),
            'father_name'             => $this->askRequired('Father name'),
            'gender'                  => $this->mapChoice('Gender', ['Male' => 1, 'Female' => 2]),
            'marital_status'          => $this->mapChoice('Marital status', ['Single' => 1, 'Married' => 2]),
            'religion'                => $this->askRequired('Religion'),
            'ethnic_group'            => $this->askRequired('Ethnic group'),
            'nrc_no'                  => $this->askRequired('NRC no.'),
            'dob'                     => $this->askRequired('Date of birth (YYYY-MM-DD)'),
            'start_date'              => $this->askRequired('Employment start date (YYYY-MM-DD)'),
            'phone'                   => $this->askRequired('Phone'),
            'address'                 => $this->askRequired('Current address'),
            'uniform_size'            => $this->choice('Uniform size', ['S', 'M', 'L', 'XL', 'XXL']),
            'education_qualification' => $this->askRequired('Education qualification'),
            'work_experience'         => $this->askRequired('Work experience'),
            'home_address'            => $this->ask('Home address (optional)', null),
            'note'                    => $this->ask('Note (optional)', null),
        ];

        $this->info('── Staff Role ───────────────────────────────────');

        $positions = StaffPosition::orderBy('name')->pluck('name', 'id')->toArray();
        if (empty($positions)) {
            $this->warn('No staff positions found.');
            $position   = StaffPosition::create(['name' => $this->askRequired('New staff position name')]);
            $positionId = $position->id;
        } else {
            $positionName = $this->choice('Position', array_values($positions));
            $positionId   = array_search($positionName, $positions);
        }

        $sites = Site::orderBy('name')->pluck('name', 'id')->toArray();
        if (empty($sites)) {
            $this->warn('No sites found.');
            $site   = Site::create(['name' => $this->askRequired('New site name')]);
            $siteId = $site->id;
        } else {
            $siteName = $this->choice('Site', array_values($sites));
            $siteId   = array_search($siteName, $sites);
        }

        $roleData = [
            'staff_position_id'    => (int) $positionId,
            'site_id'              => (int) $siteId,
            'salary'               => (int) $this->askRequired('Salary'),
            'overtime_hourly_rate' => (int) $this->askRequired('Overtime hourly rate'),
            'start_date'           => $this->askRequired('Role start date (YYYY-MM-DD)'),
        ];

        return [$profileData, $roleData];
    }

    private function askRequired(string $label): string
    {
        do {
            $value = $this->ask("{$label} *");
            if (trim((string) $value) === '') {
                $this->error("{$label} is required and cannot be empty.");
            }
        } while (trim((string) $value) === '');

        return $value;
    }

    private function mapChoice(string $label, array $map): int
    {
        $choice = $this->choice($label, array_keys($map));
        return $map[$choice];
    }
}
