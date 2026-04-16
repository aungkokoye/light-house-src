<?php

namespace App\Http\Requests\Admin\Concerns;

trait HasUserProfileRules
{
    public function attributes(): array
    {
        return [
            'name'     => 'name',
            'email'    => 'email',
            'password' => 'password',
            'role'     => 'role',

            'company_profile.name'        => 'company name',
            'company_profile.role'        => 'role / title',
            'company_profile.description' => 'description',
            'company_profile.address'     => 'address',
            'company_profile.phone'       => 'phone',

            'staff_profile.full_name'  => 'full name',
            'staff_profile.nrc_no'     => 'NRC no.',
            'staff_profile.dob'        => 'date of birth',
            'staff_profile.address'    => 'address',
            'staff_profile.phone'      => 'phone',
            'staff_profile.start_date' => 'start date',

            'staff_role.staff_position_id' => 'position',
            'staff_role.site_id'           => 'site',
            'staff_role.salary'            => 'salary',
            'staff_role.start_date'        => 'start date',
        ];
    }

    private function permissionRules(): array
    {
        return [
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ];
    }

    private function customerRules(): array
    {
        if ($this->input('role') !== 'customer') {
            return [];
        }

        return [
            'company_profile'             => ['required', 'array'],
            'company_profile.name'        => ['required', 'string', 'max:255'],
            'company_profile.role'        => ['required', 'string', 'max:255'],
            'company_profile.description' => ['nullable', 'string', 'max:10000'],
            'company_profile.address'     => ['required', 'string', 'max:2000'],
            'company_profile.phone'       => ['required', 'string', 'max:50'],
        ];
    }

    private function staffRules(): array
    {
        if ($this->input('role') === 'customer') {
            return [];
        }

        return [
            'staff_profile'                => ['required', 'array'],
            'staff_profile.full_name'      => ['required', 'string', 'max:255'],
            'staff_profile.nrc_no'         => ['required', 'string', 'max:100'],
            'staff_profile.dob'            => ['required', 'date'],
            'staff_profile.address'        => ['required', 'string', 'max:2000'],
            'staff_profile.phone'          => ['required', 'string', 'max:50'],
            'staff_profile.start_date'     => ['required', 'date'],

            'staff_role'                   => ['required', 'array'],
            'staff_role.staff_position_id' => ['required', 'exists:staff_positions,id'],
            'staff_role.site_id'           => ['required', 'exists:sites,id'],
            'staff_role.salary'            => ['required', 'integer', 'min:0'],
            'staff_role.start_date'        => ['required', 'date'],
        ];
    }
}
