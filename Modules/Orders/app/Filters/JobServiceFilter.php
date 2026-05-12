<?php

namespace Modules\Orders\Filters;

use Illuminate\Database\Eloquent\Builder;

class JobServiceFilter
{
    private const array SORTABLE = ['id', 'name', 'created_at'];

    public function __construct(private Builder $query) {}

    public static function for(Builder $query): static
    {
        return new static($query);
    }

    public function search(?string $term): static
    {
        if ($term = trim((string) $term)) {
            $this->query->whereRaw('MATCH(name, description) AGAINST(? IN BOOLEAN MODE)', [$term . '*']);
        }

        return $this;
    }

    public function sort(string $by = 'name', string $dir = 'asc'): static
    {
        $by  = in_array($by, self::SORTABLE) ? $by : 'name';
        $dir = $dir === 'desc' ? 'desc' : 'asc';

        $this->query->orderBy($by, $dir);

        return $this;
    }

    public function query(): Builder
    {
        return $this->query;
    }
}
