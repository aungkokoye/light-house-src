<?php

namespace Modules\Orders\Http\Controllers;

use App\Concerns\AuditableCrud;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Orders\Http\Requests\StoreInvoiceRequest;
use Modules\Orders\Http\Requests\UpdateInvoiceRequest;
use App\Models\User;
use Modules\Orders\Models\Invoice;
use Modules\Orders\Models\Payment;
use Modules\Orders\Services\InvoiceManager;

class InvoiceController extends Controller
{
    use AuditableCrud;

    const int DEFAULT_PER_PAGE = 15;
    const array PER_PAGE_LIST  = [5, 10, 15, 25, 50];

    public function __construct(private readonly InvoiceManager $manager) {}

    public function customers(Request $request): JsonResponse
    {
        $search = trim($request->input('search', ''));

        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $customers = User::role('customer')
            ->whereHas('companyProfile')
            ->with('companyProfile:user_id,name')
            ->select('id', 'name', 'email')
            ->where(function ($q) use ($search) {
                $q->where('name', 'like', $search . '%')
                  ->orWhere('email', 'like', $search . '%')
                  ->orWhereHas('companyProfile', fn($q2) => $q2->where('name', 'like', $search . '%'));
            })
            ->orderBy('name')
            ->limit(20)
            ->get();

        return response()->json($customers);
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = in_array((int) $request->input('per_page'), self::PER_PAGE_LIST)
            ? (int) $request->input('per_page')
            : self::DEFAULT_PER_PAGE;

        return response()->json($this->manager->list($request, $perPage));
    }

    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        $invoice = $this->manager->create($request->validated());
        $this->auditCreated($invoice, $this->snapshot($invoice));

        return response()->json($invoice, 201);
    }

    public function show(Invoice $invoice): JsonResponse
    {
        return response()->json($this->manager->show($invoice));
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice): JsonResponse
    {
        $invoice->loadMissing(['customer:id,name,email', 'jobs.service:id,name', 'jobs.product:id,name', 'payments.bank:id,name', 'createdBy:id,name']);
        $oldValues = $this->snapshot($invoice);

        $updated = $this->manager->update($invoice, $request->validated());
        $this->auditUpdated($updated, $oldValues, $this->snapshot($updated));

        return response()->json($updated);
    }

    public function destroy(Invoice $invoice): JsonResponse
    {
        $invoice->load(['customer:id,name,email', 'jobs.service:id,name', 'jobs.product:id,name', 'payments.bank:id,name', 'createdBy:id,name']);
        $this->auditDeleted($invoice, $this->snapshot($invoice));
        $this->manager->delete($invoice);

        return response()->json(['message' => 'Invoice deleted successfully.']);
    }

    private function snapshot(Invoice $invoice): array
    {
        $invoice->loadMissing([
            'customer:id,name,email',
            'jobs.service:id,name',
            'jobs.product:id,name',
            'payments.bank:id,name',
            'createdBy:id,name',
        ]);

        $attrs = $this->filterAuditValues($invoice->getAttributes());
        unset($attrs['customer_id'], $attrs['created_by']);

        $typeMap  = [Payment::TYPE_CASH => 'Cash', Payment::TYPE_BANK => 'Bank', Payment::TYPE_OTHER => 'Other'];
        $stageMap = [Payment::STAGE_ADVANCE => 'Advance', Payment::STAGE_FINAL => 'Final'];

        return array_merge($attrs, [
            'customer'   => ['id' => $invoice->customer?->id, 'name' => $invoice->customer?->name],
            'created_by' => ['id' => $invoice->createdBy?->id, 'name' => $invoice->createdBy?->name],
            'jobs'       => $invoice->jobs->map(fn($j) => [
                'service'       => $j->service?->name,
                'product'       => $j->product?->name,
                'quantity'      => $j->quantity,
                'unit_price'    => $j->unit_price,
                'total'         => $j->total,
                'delivery_date' => $j->delivery_date?->toDateString(),
            ])->toArray(),
            'payments'   => $invoice->payments->map(fn($p) => [
                'type'         => $typeMap[$p->type_id]  ?? 'Unknown',
                'bank'         => $p->bank?->name,
                'stage'        => $stageMap[$p->stage]   ?? 'Unknown',
                'amount'       => $p->amount,
                'payment_date' => $p->payment_date?->toDateString(),
                'note'         => $p->note,
            ])->toArray(),
        ]);
    }
}
