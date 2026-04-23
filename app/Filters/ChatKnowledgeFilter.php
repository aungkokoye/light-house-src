<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class ChatKnowledgeFilter
{
    private const array SORTABLE = ['id', 'category', 'title', 'sort_order', 'active', 'created_at'];

    public function __construct(private Builder $query) {}

    public static function for(Builder $query): static
    {
        return new static($query);
    }

    public function search(?string $term): static
    {
        if ($term) {
            $this->query->where(function (Builder $q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                    ->orWhere('category', 'like', "%{$term}%")
                    ->orWhere('content', 'like', "%{$term}%");
            });
        }

        return $this;
    }

    public function category(?string $category): static
    {
        if ($category) {
            $this->query->where('category', $category);
        }

        return $this;
    }

    public function active(?string $active): static
    {
        if ($active !== null && $active !== '') {
            $this->query->where('active', (bool) $active);
        }

        return $this;
    }

    public function sort(string $by = 'sort_order', string $dir = 'asc'): static
    {
        $by  = in_array($by, self::SORTABLE) ? $by : 'sort_order';
        $dir = $dir === 'desc' ? 'desc' : 'asc';

        $this->query->orderBy($by, $dir);

        return $this;
    }

    public function query(): Builder
    {
        return $this->query;
    }
}
