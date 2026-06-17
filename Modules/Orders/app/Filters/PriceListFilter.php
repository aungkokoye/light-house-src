<?php

namespace Modules\Orders\Filters;

use Illuminate\Database\Eloquent\Builder;

class PriceListFilter
{
    private const array SORTABLE = ['id', 'price', 'created_at'];

    public function __construct(private Builder $query) {}

    public static function for(Builder $query): static
    {
        return new static($query);
    }

    public function search(?string $term): static
    {
        if ($term = trim((string) $term)) {
            $this->query->where('product_description', 'like', '%' . $term . '%');
        }

        return $this;
    }

    public function jobService(?int $jobServiceId): static
    {
        if ($jobServiceId) {
            $this->query->where('job_service_id', $jobServiceId);
        }

        return $this;
    }

    public function sort(string $by = 'created_at', string $dir = 'desc'): static
    {
        $by  = in_array($by, self::SORTABLE) ? $by : 'created_at';
        $dir = $dir === 'desc' ? 'desc' : 'asc';

        $this->query->orderBy($by, $dir);

        return $this;
    }

    public function query(): Builder
    {
        return $this->query;
    }
}
