<?php

namespace Modules\Orders\Http\Controllers;

use App\Concerns\AuditableCrud;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Orders\Http\Requests\StorePaymentRequest;
use Modules\Orders\Http\Requests\UpdatePaymentRequest;
use Modules\Orders\Models\Payment;
use Modules\Orders\Services\PaymentManager;

class PaymentController extends Controller
{
    use AuditableCrud;

    public function __construct(private readonly PaymentManager $paymentManager) {}

    public function meta(): JsonResponse
    {
        return response()->json([
            'stages'      => Payment::stageOptions(),
            'stage_final' => Payment::STAGE_FINAL,
        ]);
    }

    public function store(StorePaymentRequest $request): JsonResponse
    {
        $payment = $this->paymentManager->create($request->validated());
        $this->auditCreated($payment, $this->snapshot($payment));

        return response()->json($payment, 201);
    }

    public function show(Payment $payment): JsonResponse
    {
        return response()->json($this->paymentManager->show($payment));
    }

    public function update(UpdatePaymentRequest $request, Payment $payment): JsonResponse
    {
        $payment->load('paymentType:id,name', 'createdBy:id,name');
        $oldValues = $this->snapshot($payment);

        $updated = $this->paymentManager->update($payment, $request->validated());
        $this->auditUpdated($updated, $oldValues, $this->snapshot($updated));

        return response()->json($updated);
    }

    public function sendReceipt(Payment $payment): JsonResponse
    {
        $payment->loadMissing([
            'invoice:id,invoice_no,total,customer_id',
            'invoice.customer:id,name,email,company_name',
            'paymentType:id,name',
            'createdBy:id,name',
        ]);

        $customer = $payment->invoice?->customer;

        if (! $customer?->email) {
            return response()->json(['message' => 'Customer has no email address.'], 422);
        }

        $customer->notify(new \App\Notifications\PaymentReceiptNotification($payment));

        return response()->json(['message' => 'Receipt sent to ' . $customer->email]);
    }

    public function destroy(Payment $payment): JsonResponse
    {
        $payment->load('paymentType:id,name', 'createdBy:id,name');
        $this->auditDeleted($payment, $this->snapshot($payment));
        $this->paymentManager->delete($payment);

        return response()->json(['message' => 'Payment deleted successfully.']);
    }

    private function snapshot(Payment $payment): array
    {
        $attrs = $this->filterAuditValues($payment->getAttributes());

        unset($attrs['payment_type_id'], $attrs['created_by']);

        return array_merge($attrs, [
            'payment_type' => ['id' => $payment->paymentType?->id,  'name' => $payment->paymentType?->name],
            'created_by'   => ['id' => $payment->createdBy?->id,    'name' => $payment->createdBy?->name],
        ]);
    }
}
