<?php

namespace Modules\Orders\Services;

use App\Concerns\AuditableCrud;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Modules\Orders\Filters\PaymentTypeFilter;
use Modules\Orders\Models\PaymentType;

class PaymentTypeManager
{
    use AuditableCrud;

    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        return PaymentType::with('createdBy:id,name')->orderBy('name')->get();
    }

    public function list(Request $request, int $perPage = 20): LengthAwarePaginator
    {
        $query = PaymentTypeFilter::for(PaymentType::withCount('payments')->with('createdBy:id,name'))
            ->search($request->input('search'))
            ->sort($request->input('sort_by', 'name'), $request->input('sort_dir', 'asc'))
            ->query();

        return $query->paginate($perPage);
    }

    public function show(PaymentType $paymentType): PaymentType
    {
        return $paymentType->load('createdBy:id,name');
    }

    public function create(array $data): PaymentType
    {
        $paymentType = PaymentType::create([...$data, 'created_by' => Auth::id()]);
        $paymentType->load('createdBy:id,name');
        $this->auditCreated($paymentType, $this->snapshot($paymentType));

        return $paymentType;
    }

    public function update(PaymentType $paymentType, array $data): PaymentType
    {
        $paymentType->load('createdBy:id,name');
        $oldValues = $this->snapshot($paymentType);
        $paymentType->update($data);
        $this->auditUpdated($paymentType, $oldValues, $this->snapshot($paymentType));

        return $paymentType;
    }

    public function delete(PaymentType $paymentType): void
    {
        $paymentType->load('createdBy:id,name');
        $this->auditDeleted($paymentType, $this->snapshot($paymentType));
        $paymentType->delete();
    }

    private function snapshot(PaymentType $paymentType): array
    {
        return array_merge(
            $this->filterAuditValues($paymentType->getAttributes()),
            [
                'created_by' => ['id' => $paymentType->createdBy?->id, 'name' => $paymentType->createdBy?->name ]
            ],
        );
    }
}
