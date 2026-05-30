<?php

namespace Modules\Orders\Services;

use App\Concerns\AuditableCrud;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Modules\Orders\Filters\CustomerFilter;
use Modules\Orders\Models\Customer;

class CustomerManager
{
    use AuditableCrud;

    public function search(?string $term): Collection
    {
        if (!$term || strlen($term) < 2) {
            return collect();
        }

        return Customer::where(function ($q) use ($term) {
            $q->where('name', 'like', "{$term}%")
              ->orWhere('email', 'like', "{$term}%")
              ->orWhere('company_name', 'like', "{$term}%");
        })->select('id', 'name', 'email', 'company_name')->orderBy('name')->limit(20)->get();
    }

    public function list(Request $request, int $perPage = 20): LengthAwarePaginator
    {
        $query = CustomerFilter::for(Customer::with('createdBy:id,name')->withCount('invoices'))
            ->search($request->input('search'))
            ->sort($request->input('sort_by', 'name'), $request->input('sort_dir', 'asc'))
            ->query();

        return $query->paginate($perPage);
    }

    public function show(Customer $customer): Customer
    {
        return $customer->load('createdBy:id,name');
    }

    public function create(array $data): Customer
    {
        $customer = Customer::create([...$data, 'created_by' => Auth::id()]);
        $customer->load('createdBy:id,name');
        $this->auditCreated($customer, $this->snapshot($customer));

        return $customer;
    }

    public function update(Customer $customer, array $data): Customer
    {
        $customer->load('createdBy:id,name');
        $old = $this->snapshot($customer);
        $customer->update($data);
        $this->auditUpdated($customer, $old, $this->snapshot($customer));

        return $customer;
    }

    public function delete(Customer $customer): void
    {
        $customer->load('createdBy:id,name');
        $this->auditDeleted($customer, $this->snapshot($customer));
        $customer->delete();
    }

    private function snapshot(Customer $customer): array
    {
        return array_merge(
            $this->filterAuditValues($customer->getAttributes()),
            ['created_by' => ['id' => $customer->createdBy?->id, 'name' => $customer->createdBy?->name]]
        );
    }
}
