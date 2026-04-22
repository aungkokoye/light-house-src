<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class AuditLogFilter
{
    private const array SORTABLE = ['id', 'user_email', 'ip_address', 'created_at'];

    public function __construct(private Builder $query) {}

    public static function for(Builder $query): static
    {
        return new static($query);
    }

    public function search(?string $term): static
    {
        if ($term) {
            $this->query->where('user_email', 'like', "%{$term}%");
        }

        return $this;
    }

    public function event(?string $event): static
    {
        if ($event) {
            $this->query->where('event', $event);
        }

        return $this;
    }

    public function sort(string $by = 'created_at', string $dir = 'desc'): static
    {
        $by  = in_array($by, self::SORTABLE) ? $by : 'created_at';
        $dir = $dir === 'asc' ? 'asc' : 'desc';

        $this->query->orderBy($by, $dir);

        return $this;
    }

    public function query(): Builder
    {
        return $this->query;
    }
}