<?php

namespace Modules\Orders\Filters;

use Illuminate\Database\Eloquent\Builder;

class CustomerFilter
{
    private const array SORTABLE = ['id', 'name', 'email', 'company_name', 'phone', 'created_at'];

    public function __construct(private Builder $query) {}

    public static function for(Builder $query): static
    {
        return new static($query);
    }

    public function search(?string $term): static
    {
        if ($term) {
            $this->query->where(function ($q) use ($term) {
                $q->where('name', 'like', "{$term}%")
                  ->orWhere('email', 'like', "{$term}%")
                  ->orWhere('company_name', 'like', "{$term}%")
                  ->orWhere('phone', 'like', "{$term}%");
            });
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
