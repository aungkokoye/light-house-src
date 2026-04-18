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
            'name'        => $this->ask('Company name'),
            'role'        => $this->ask('Your role / title at the company'),
            'description' => $this->ask('Description (optional)', null),
            'address'     => $this->ask('Address'),
            'phone'       => $this->ask('Phone'),
        ];
    }

    private function askStaffProfile(): array
    {
        $this->info('── Staff Profile ────────────────────────────────');

        $profileData = [
            'full_name'  => $this->ask('Full name'),
            'nrc_no'     => $this->ask('NRC no.'),
            'dob'        => $this->ask('Date of birth (YYYY-MM-DD)'),
            'address'    => $this->ask('Address'),
            'phone'      => $this->ask('Phone'),
            'start_date' => $this->ask('Employment start date (YYYY-MM-DD)'),
        ];

        $this->info('── Staff Role ───────────────────────────────────');

        $positions = StaffPosition::orderBy('name')->pluck('name', 'id')->toArray();
        if (empty($positions)) {
            $this->warn('No staff positions found.');
            $newPositionName = $this->ask('Enter a new staff position name to create');
            $position = StaffPosition::create(['name' => $newPositionName]);
            $positionId = $position->id;
            $positionName = $position->name;
        } else {
            $positionName = $this->choice('Position', array_values($positions));
            $positionId   = array_search($positionName, $positions);
        }

        $sites = Site::orderBy('name')->pluck('name', 'id')->toArray();
        if (empty($sites)) {
            $this->warn('No sites found.');
            $newSiteName = $this->ask('Enter a new site name to create');
            $site = Site::create(['name' => $newSiteName]);
            $siteId = $site->id;
            $siteName = $site->name;
        } else {
            $siteName = $this->choice('Site', array_values($sites));
            $siteId   = array_search($siteName, $sites);
        }

        $salary    = $this->ask('Salary (integer)');
        $startDate = $this->ask('Role start date (YYYY-MM-DD)');

        $roleData = [
            'staff_position_id' => (int) $positionId,
            'site_id'           => (int) $siteId,
            'salary'            => (int) $salary,
            'start_date'        => $startDate,
        ];

        return [$profileData, $roleData];
    }
}
