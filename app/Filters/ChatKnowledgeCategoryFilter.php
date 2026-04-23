<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class ChatKnowledgeCategoryFilter
{
    private const array SORTABLE = ['id', 'name', 'sort_order', 'created_at'];

    public function __construct(private Builder $query) {}

    public static function for(Builder $query): static
    {
        return new static($query);
    }

    public function search(?string $term): static
    {
        if ($term) {
            $this->query->where(function (Builder $q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
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
