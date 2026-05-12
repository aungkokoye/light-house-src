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
        $payment->load('bank:id,name', 'createdBy:id,name');
        $oldValues = $this->snapshot($payment);

        $updated = $this->paymentManager->update($payment, $request->validated());
        $this->auditUpdated($updated, $oldValues, $this->snapshot($updated));

        return response()->json($updated);
    }

    public function destroy(Payment $payment): JsonResponse
    {
        $payment->load('bank:id,name', 'createdBy:id,name');
        $this->auditDeleted($payment, $this->snapshot($payment));
        $this->paymentManager->delete($payment);

        return response()->json(['message' => 'Payment deleted successfully.']);
    }

    private function snapshot(Payment $payment): array
    {
        $attrs = $this->filterAuditValues($payment->getAttributes());

        unset($attrs['bank_id'], $attrs['created_by']);

        return array_merge($attrs, [
            'bank'       => ['id' => $payment->bank?->id,      'name' => $payment->bank?->name],
            'created_by' => ['id' => $payment->createdBy?->id, 'name' => $payment->createdBy?->name],
        ]);
    }
}
