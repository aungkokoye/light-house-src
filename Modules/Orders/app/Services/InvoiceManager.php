<?php

namespace Modules\Orders\Services;

use App\Concerns\DispatchesAuditEvents;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Orders\Filters\InvoiceFilter;
use Modules\Orders\Models\Invoice;
use Modules\Orders\Models\InvoiceJob;
use Modules\Orders\Models\Payment;

class InvoiceManager
{
    use DispatchesAuditEvents;

    public function list(Request $request, int $perPage): LengthAwarePaginator
    {
        $query = Invoice::with(['customer:id,name,email', 'createdBy:id,name'])
            ->addSelect([
                'paid_amount' => DB::table('payments')
                    ->selectRaw('COALESCE(SUM(amount), 0)')
                    ->whereColumn('invoice_id', 'invoices.id'),
            ]);

        return InvoiceFilter::for($query)
            ->search($request->input('search'))
            ->sort($request->input('sort_by', 'created_at'), $request->input('sort_dir', 'desc'))
            ->query()
            ->paginate($perPage);
    }

    public function sendToCustomer(Invoice $invoice): void
    {
        $invoice->loadMissing([
            'customer:id,name,email',
            'jobs.service:id,name',
            'jobs.product:id,name',
            'payments.bank:id,name',
            'createdBy:id,name',
        ]);

        $invoice->customer->notify(new \App\Notifications\InvoiceNotification($invoice));
    }

    public function show(Invoice $invoice): Invoice
    {
        return $invoice->load([
            'customer:id,name,email',
            'jobs.service:id,name',
            'jobs.product:id,name',
            'payments.bank:id,name',
            'payments.createdBy:id,name',
            'createdBy:id,name',
        ]);
    }

    public function create(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            $jobs     = $data['jobs'];
            $discount = (int) ($data['discount'] ?? 0);
            $total    = $this->calcTotal($jobs, $discount);

            $invoice = Invoice::create([
                'customer_id' => $data['customer_id'],
                'discount'    => $discount,
                'total'       => $total,
                'note'        => $data['note'] ?? null,
                'created_by'  => Auth::id(),
            ]);

            $this->syncJobs($invoice, $jobs);

            $p = $data['payment'];
            Payment::create([
                'invoice_id'   => $invoice->id,
                'type_id'      => $p['type_id'],
                'bank_id'      => $p['bank_id'] ?? null,
                'stage'        => $p['stage'],
                'amount'       => (int) $p['amount'],
                'payment_date' => $p['payment_date'],
                'note'         => $p['note'] ?? null,
                'created_by'   => Auth::id(),
            ]);

            return $invoice->load([
                'customer:id,name,email',
                'jobs.service:id,name',
                'jobs.product:id,name',
                'payments.bank:id,name',
                'createdBy:id,name',
            ]);
        });
    }

    public function update(Invoice $invoice, array $data): Invoice
    {
        return DB::transaction(function () use ($invoice, $data) {
            $jobs     = $data['jobs'];
            $discount = (int) ($data['discount'] ?? 0);
            $total    = $this->calcTotal($jobs, $discount);

            $invoice->update([
                'customer_id' => $data['customer_id'],
                'discount'    => $discount,
                'total'       => $total,
                'note'        => $data['note'] ?? null,
            ]);

            $invoice->jobs()->delete();
            $this->syncJobs($invoice, $jobs);

            // Delete payments omitted from the submitted list
            $submittedIds = collect($data['payments'] ?? [])
                ->filter(fn($p) => !empty($p['id']))
                ->pluck('id')
                ->map(fn($id) => (int) $id);
            $invoice->payments()->whereNotIn('id', $submittedIds)->delete();

            // Update existing / create new payments
            $existingPayments = $invoice->payments->keyBy('id');
            foreach ($data['payments'] ?? [] as $p) {
                if (!empty($p['id'])) {
                    $existingPayments[(int) $p['id']]?->update([
                        'type_id'      => $p['type_id'],
                        'bank_id'      => $p['type_id'] == Payment::TYPE_BANK ? ($p['bank_id'] ?? null) : null,
                        'stage'        => $p['stage'],
                        'amount'       => (int) $p['amount'],
                        'payment_date' => $p['payment_date'],
                        'note'         => $p['note'] ?? null,
                    ]);
                } else {
                    Payment::create([
                        'invoice_id'   => $invoice->id,
                        'type_id'      => $p['type_id'],
                        'bank_id'      => $p['type_id'] == Payment::TYPE_BANK ? ($p['bank_id'] ?? null) : null,
                        'stage'        => $p['stage'],
                        'amount'       => (int) $p['amount'],
                        'payment_date' => $p['payment_date'],
                        'note'         => $p['note'] ?? null,
                        'created_by'   => Auth::id(),
                    ]);
                }
            }

            return $invoice->refresh()->load([
                'customer:id,name,email',
                'jobs.service:id,name',
                'jobs.product:id,name',
                'payments.bank:id,name',
                'createdBy:id,name',
            ]);
        });
    }

    public function delete(Invoice $invoice): void
    {
        $invoice->delete();
    }

    private function calcTotal(array $jobs, int $discount): int
    {
        $subtotal = array_sum(array_map(
            fn($j) => (int) $j['quantity'] * (int) $j['unit_price'],
            $jobs
        ));

        return max(0, $subtotal - $discount);
    }

    private function syncJobs(Invoice $invoice, array $jobs): void
    {
        $now  = now();
        $rows = array_map(fn($job) => [
            'invoice_id'    => $invoice->id,
            'service_id'    => $job['service_id'],
            'product_id'    => $job['product_id'],
            'quantity'      => (int) $job['quantity'],
            'unit_price'    => (int) $job['unit_price'],
            'total'         => (int) $job['quantity'] * (int) $job['unit_price'],
            'delivery_date' => Carbon::parse($job['delivery_date'])->format('Y-m-d'),
            'note'          => $job['note'] ?? null,
            'created_by'    => Auth::id(),
            'created_at'    => $now,
            'updated_at'    => $now,
        ], $jobs);

        InvoiceJob::insert($rows);
    }
}
