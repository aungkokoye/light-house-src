<?php

namespace Modules\Orders\Services;

use Illuminate\Support\Facades\Auth;
use Modules\Orders\Models\Payment;

class PaymentManager
{
    public function create(array $data): Payment
    {
        $payment = Payment::create([...$data, 'created_by' => Auth::id()]);

        return $payment->load('bank:id,name', 'createdBy:id,name');
    }

    public function show(Payment $payment): Payment
    {
        return $payment->load('bank:id,name', 'createdBy:id,name');
    }

    public function update(Payment $payment, array $data): Payment
    {
        $payment->update($data);

        return $payment->refresh()->load('bank:id,name', 'createdBy:id,name');
    }

    public function delete(Payment $payment): void
    {
        $payment->delete();
    }
}
