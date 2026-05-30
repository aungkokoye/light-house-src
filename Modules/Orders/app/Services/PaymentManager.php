<?php

namespace Modules\Orders\Services;

use Illuminate\Support\Facades\Auth;
use Modules\Orders\Models\Payment;

class PaymentManager
{
    public function create(array $data): Payment
    {
        $payment = Payment::create([...$data, 'created_by' => Auth::id()]);

        return $payment->load('paymentType:id,name', 'createdBy:id,name');
    }

    public function show(Payment $payment): Payment
    {
        return $payment->load('paymentType:id,name', 'createdBy:id,name');
    }

    public function update(Payment $payment, array $data): Payment
    {
        $payment->update([
            'payment_type_id' => $data['payment_type_id'],
            'stage'        => $data['stage'],
            'amount'       => (int) $data['amount'],
            'payment_date' => $data['payment_date'],
            'note'         => $data['note'] ?? null,
        ]);

        return $payment->refresh()->load('paymentType:id,name', 'createdBy:id,name');
    }

    public function delete(Payment $payment): void
    {
        $payment->delete();
    }
}
