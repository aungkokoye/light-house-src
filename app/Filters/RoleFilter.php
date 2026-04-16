<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class RoleFilter
{
    private const array SORTABLE = ['id', 'name', 'created_at', 'updated_at'];

    public function __construct(private Builder $query) {}

    public static function for(Builder $query): static
    {
        return new static($query);
    }

    public function search(?string $term): static
    {
        if ($term) {
            $this->query->where('name', 'like', "%{$term}%");
        }

        return $this;
    }

    public function sort(string $by = 'updated_at', string $dir = 'desc'): static
    {
        $by  = in_array($by, self::SORTABLE) ? $by : 'updated_at';
        $dir = $dir === 'asc' ? 'asc' : 'desc';

        $this->query->orderBy($by, $dir);

        return $this;
    }

    public function query(): Builder
    {
        return $this->query;
    }
}
