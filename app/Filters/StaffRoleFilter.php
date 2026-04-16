<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class StaffRoleFilter
{
    private const SORTABLE = ['id', 'position_name', 'start_date', 'end_date'];

    public function __construct(private Builder $query) {}

    public static function for(Builder $query): static
    {
        return new static($query);
    }

    public function sort(string $by = '', string $dir = 'desc'): static
    {
        $dir = $dir === 'asc' ? 'asc' : 'desc';

        if (! in_array($by, self::SORTABLE)) {
            // Default: active (no end_date) first, then most recent start_date
            $this->query->orderByRaw('(end_date IS NULL) DESC')->orderBy('start_date', 'desc');

            return $this;
        }

        if ($by === 'position_name') {
            $this->query
                ->join('staff_positions', 'staff_roles.staff_position_id', '=', 'staff_positions.id')
                ->select('staff_roles.*')
                ->orderBy('staff_positions.name', $dir);
        } else {
            $this->query->orderBy($by, $dir);
        }

        return $this;
    }

    public function query(): Builder
    {
        return $this->query;
    }
}
