<?php

namespace Modules\Orders\Filters;

use Illuminate\Database\Eloquent\Builder;

class ProductFilter
{
    private const array SORTABLE = ['id', 'name', 'created_at', 'per_price'];

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

        // 'per_price' uses the 'current_price' subquery alias added by ProductManager
        $this->query->orderBy($by === 'per_price' ? 'current_price' : $by, $dir);

        return $this;
    }

    public function query(): Builder
    {
        return $this->query;
    }
}
