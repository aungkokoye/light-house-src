<?php

namespace Modules\Orders\Filters;

use Illuminate\Database\Eloquent\Builder;

class InvoiceFilter
{
    private const array SORTABLE = ['id', 'invoice_no', 'total', 'created_at'];

    public function __construct(private Builder $query) {}

    public static function for(Builder $query): static
    {
        return new static($query);
    }

    public function search(?string $term): static
    {
        if ($term = trim((string) $term)) {
            $this->query->where(function ($q) use ($term) {
                $q->where('invoice_no', 'like', $term . '%')
                  ->orWhereHas('customer', fn($q2) => $q2->where('name', 'like', '%' . $term . '%'));
            });
        }

        return $this;
    }

    public function status(?string $status): static
    {
        $paidSub = 'COALESCE((SELECT SUM(amount) FROM payments WHERE invoice_id = invoices.id), 0)';

        if ($status === 'paid') {
            $this->query->whereRaw("invoices.total - ({$paidSub}) = 0");
        } elseif ($status === 'credit') {
            $this->query->whereRaw("invoices.total - ({$paidSub}) != 0");
        }

        return $this;
    }

    public function createdFrom(?string $date): static
    {
        if ($date) {
            $this->query->whereDate('invoices.created_at', '>=', $date);
        }

        return $this;
    }

    public function createdTo(?string $date): static
    {
        if ($date) {
            $this->query->whereDate('invoices.created_at', '<=', $date);
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
