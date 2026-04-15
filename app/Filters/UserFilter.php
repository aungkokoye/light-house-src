<?php

namespace App\Filters;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UserFilter
{
    private const SORTABLE = ['id', 'name', 'email', 'email_verified_at', 'activated', 'updated_at'];

    public function __construct(private Builder $query) {}

    public static function for(Builder $query): static
    {
        return new static($query);
    }

    public function search(?string $term): static
    {
        if ($term) {
            $this->query->where(fn($q) => $q
                ->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhereHas('companyProfile', fn($q) => $q->where('name', 'like', "%{$term}%"))
            );
        }

        return $this;
    }

    public function position(?string $positionId): static
    {
        if ($positionId === null) {
            return $this;
        }

        $this->query->where('type', User::STAFF_TYPE_ID);

        if ($positionId !== '') {
            $this->query->whereHas('staffRoles', fn($q) => $q
                ->where('staff_position_id', (int) $positionId)
                ->whereNull('end_date')
            );
        }

        return $this;
    }

    public function type(?string $type): static
    {
        if ($type !== null && $type !== '') {
            $this->query->where('type', (int) $type);
        }

        return $this;
    }

    public function role(?string $role): static
    {
        if ($role) {
            $this->query->whereHas('roles', fn($q) => $q->where('name', $role));
        }

        return $this;
    }

    public function activated(?string $value): static
    {
        if ($value !== null && $value !== '') {
            $this->query->where('activated', filter_var($value, FILTER_VALIDATE_BOOLEAN));
        }

        return $this;
    }

    public function emailVerified(?string $value): static
    {
        if ($value !== null && $value !== '') {
            $value === 'true'
                ? $this->query->whereNotNull('email_verified_at')
                : $this->query->whereNull('email_verified_at');
        }

        return $this;
    }

    public function updatedFrom(?string $date): static
    {
        if ($date) {
            $this->query->whereDate('updated_at', '>=', $date);
        }

        return $this;
    }

    public function updatedTo(?string $date): static
    {
        if ($date) {
            $this->query->whereDate('updated_at', '<=', $date);
        }

        return $this;
    }

    public function sort(string $by = 'id', string $dir = 'asc'): static
    {
        $by  = in_array($by, self::SORTABLE) ? $by : 'id';
        $dir = $dir === 'asc' ? 'asc' : 'desc';

        $this->query->orderBy($by, $dir);

        return $this;
    }

    public function query(): Builder
    {
        return $this->query;
    }
}
