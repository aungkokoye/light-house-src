<?php

namespace Modules\Orders\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Concerns\HasAbilities;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Orders\Http\Requests\StorePaymentTypeRequest;
use Modules\Orders\Http\Requests\UpdatePaymentTypeRequest;
use Modules\Orders\Models\PaymentType;
use Modules\Orders\Services\PaymentTypeManager;

class PaymentTypeController extends Controller
{
    use HasAbilities;

    public function __construct(private readonly PaymentTypeManager $paymentTypeManager) {}

    public function index(Request $request): JsonResponse
    {
        $perPage  = (int) $request->input('per_page', 20);
        $paginated = $this->paymentTypeManager->list($request, $perPage);

        return response()->json(array_merge($paginated->toArray(), [
            'can' => $this->listAbilities(PaymentType::class),
        ]));
    }

    public function store(StorePaymentTypeRequest $request): JsonResponse
    {
        return response()->json($this->paymentTypeManager->create($request->validated()), 201);
    }

    public function show(PaymentType $payment_type): JsonResponse
    {
        return response()->json(array_merge($this->paymentTypeManager->show($payment_type)->toArray(), [
            'can' => $this->resourceAbilities($payment_type),
        ]));
    }

    public function update(UpdatePaymentTypeRequest $request, PaymentType $payment_type): JsonResponse
    {
        return response()->json($this->paymentTypeManager->update($payment_type, $request->validated()));
    }

    public function destroy(PaymentType $payment_type): JsonResponse
    {
        try {
            $this->paymentTypeManager->delete($payment_type);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json(['message' => 'Cannot delete this payment type because it is used in existing payments.'], 422);
            }
            throw $e;
        }

        return response()->json(['message' => 'Payment type deleted successfully.']);
    }
}
