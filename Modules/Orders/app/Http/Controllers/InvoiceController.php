<?php

namespace Modules\Orders\Http\Controllers;

use App\Concerns\AuditableCrud;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Concerns\HasAbilities;
use Modules\Orders\Http\Requests\StoreInvoiceRequest;
use Modules\Orders\Http\Requests\UpdateInvoiceRequest;
use Modules\Orders\Models\Invoice;
use Modules\Orders\Models\InvoiceJob;
use Modules\Orders\Models\Payment;
use Modules\Orders\Services\InvoiceManager;

class InvoiceController extends Controller
{
    use AuditableCrud, HasAbilities;

    const int DEFAULT_PER_PAGE = 15;
    const array PER_PAGE_LIST  = [5, 10, 15, 25, 50];

    public function __construct(private readonly InvoiceManager $manager) {}

    public function index(Request $request): JsonResponse
    {
        $perPage   = in_array((int) $request->input('per_page'), self::PER_PAGE_LIST)
            ? (int) $request->input('per_page')
            : self::DEFAULT_PER_PAGE;
        $paginated = $this->manager->list($request, $perPage);

        return response()->json(array_merge($paginated->toArray(), [
            'can' => $this->listAbilities(Invoice::class),
        ]));
    }

    public function send(Invoice $invoice): JsonResponse
    {
        $invoice->loadMissing('customer:id,name,email');

        if (! $invoice->customer?->email) {
            return response()->json(['message' => 'Customer has no email address.'], 422);
        }

        $this->manager->sendToCustomer($invoice);

        return response()->json(['message' => 'Invoice sent to ' . $invoice->customer->email]);
    }

    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        $invoice = $this->manager->create($request->validated());
        $this->auditCreated($invoice, $this->snapshot($invoice));

        return response()->json($invoice, 201);
    }

    public function show(Invoice $invoice): JsonResponse
    {
        return response()->json(array_merge($this->manager->show($invoice)->toArray(), [
            'can' => array_merge($this->resourceAbilities($invoice), [
                'add_payment' => Gate::allows('create', Payment::class),
            ]),
        ]));
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice): JsonResponse
    {
        $invoice->loadMissing(['customer:id,name,email', 'jobs.service:id,name', 'jobs.product:id,name', 'payments.paymentType:id,name', 'createdBy:id,name']);
        $oldValues = $this->snapshot($invoice);

        $updated = $this->manager->update($invoice, $request->validated());
        $this->auditUpdated($updated, $oldValues, $this->snapshot($updated));

        return response()->json($updated);
    }

    public function destroyJob(Invoice $invoice, InvoiceJob $job): JsonResponse
    {
        if ($job->invoice_id !== $invoice->id) {
            return response()->json(['message' => 'Job does not belong to this invoice.'], 403);
        }

        if ($invoice->jobs()->count() <= 1) {
            return response()->json(['message' => 'Cannot delete the last job on an invoice.'], 422);
        }

        $job->delete();

        return response()->json(['message' => 'Job deleted successfully.']);
    }

    public function destroy(Invoice $invoice): JsonResponse
    {
        $invoice->load(['customer:id,name,email', 'jobs.service:id,name', 'jobs.product:id,name', 'payments.paymentType:id,name', 'createdBy:id,name']);
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
            'payments.paymentType:id,name',
            'createdBy:id,name',
        ]);

        $attrs = $this->filterAuditValues($invoice->getAttributes());
        unset($attrs['customer_id'], $attrs['created_by']);

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
                'note'          => $j->note,
            ])->toArray(),
            'payments'   => $invoice->payments->map(fn($p) => [
                'payment_type' => $p->paymentType?->name,
                'stage'        => $stageMap[$p->stage]   ?? 'Unknown',
                'amount'       => $p->amount,
                'payment_date' => $p->payment_date?->toDateString(),
                'note'         => $p->note,
            ])->toArray(),
        ]);
    }
}
