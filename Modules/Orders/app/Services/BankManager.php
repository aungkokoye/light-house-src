<?php

namespace Modules\Orders\Services;

use App\Concerns\AuditableCrud;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Modules\Orders\Filters\BankFilter;
use Modules\Orders\Models\Bank;

class BankManager
{
    use AuditableCrud;

    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        return Bank::with('createdBy:id,name')->orderBy('name')->get();
    }

    public function list(Request $request, int $perPage = 20): LengthAwarePaginator
    {
        $query = BankFilter::for(Bank::withCount('payments')->with('createdBy:id,name'))
            ->search($request->input('search'))
            ->sort($request->input('sort_by', 'name'), $request->input('sort_dir', 'asc'))
            ->query();

        return $query->paginate($perPage);
    }

    public function show(Bank $bank): Bank
    {
        return $bank->load('createdBy:id,name');
    }

    public function create(array $data): Bank
    {
        $bank = Bank::create([...$data, 'created_by' => Auth::id()]);
        $bank->load('createdBy:id,name');
        $this->auditCreated($bank, $this->snapshot($bank));

        return $bank;
    }

    public function update(Bank $bank, array $data): Bank
    {
        $bank->load('createdBy:id,name');
        $oldValues = $this->snapshot($bank);
        $bank->update($data);
        $this->auditUpdated($bank, $oldValues, $this->snapshot($bank));

        return $bank;
    }

    public function delete(Bank $bank): void
    {
        $bank->load('createdBy:id,name');
        $this->auditDeleted($bank, $this->snapshot($bank));
        $bank->delete();
    }

    private function snapshot(Bank $bank): array
    {
        return array_merge(
            $this->filterAuditValues($bank->getAttributes()),
            [
                'created_by' => ['id' => $bank->createdBy?->id, 'name' => $bank->createdBy?->name ]
            ],
        );
    }
}
