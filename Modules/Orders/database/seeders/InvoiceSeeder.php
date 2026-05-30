<?php

namespace Modules\Orders\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Orders\Models\Customer;
use Modules\Orders\Models\Invoice;
use Modules\Orders\Models\InvoiceJob;
use Modules\Orders\Models\JobService;
use Modules\Orders\Models\Payment;
use Modules\Orders\Models\PaymentType;
use Modules\Orders\Models\Product;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $customers  = Customer::pluck('id');
        $serviceIds = JobService::pluck('id');
        $productIds = Product::pluck('id');
        $paymentTypeIds = PaymentType::pluck('id');

        for ($i = 0; $i < 50; $i++) {
            $discount = fake()->randomElement([0, 0, 0, 5000, 10000, 20000, 50000]);

            $invoice = Invoice::create([
                'customer_id' => $customers->random(),
                'discount'    => $discount,
                'total'       => 0,
                'note'        => fake()->optional(0.4)->sentence(),
                'created_by'  => 1,
            ]);

            // 1–5 jobs
            $jobTotal = 0;
            $jobCount = rand(1, 5);
            for ($j = 0; $j < $jobCount; $j++) {
                $qty       = rand(1, 20);
                $unitPrice = fake()->randomElement([5000, 10000, 15000, 20000, 30000, 50000, 80000, 100000, 150000, 200000]);
                $total     = $qty * $unitPrice;
                $jobTotal += $total;

                InvoiceJob::create([
                    'invoice_id'    => $invoice->id,
                    'service_id'    => $serviceIds->random(),
                    'product_id'    => $productIds->random(),
                    'quantity'      => $qty,
                    'unit_price'    => $unitPrice,
                    'total'         => $total,
                    'delivery_date' => fake()->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
                    'note'          => fake()->optional(0.3)->sentence(),
                    'created_by'    => 1,
                ]);
            }

            $grandTotal = max(0, $jobTotal - $discount);
            $invoice->update(['total' => $grandTotal]);

            // Skip payments if total is zero
            if ($grandTotal === 0) {
                continue;
            }

            // 1–3 payments following validation rules:
            // - hasFinal → sum of all payments === grandTotal, last stage = FINAL
            // - !hasFinal → sum of all payments < grandTotal, all stage = ADVANCE
            $pmtCount = rand(1, 3);
            $hasFinal = (bool) rand(0, 1);
            $remaining = $grandTotal;

            for ($p = 0; $p < $pmtCount; $p++) {
                $isLast  = ($p === $pmtCount - 1);
                $isFinal = $isLast && $hasFinal;

                if ($isFinal) {
                    // Final payment takes exactly what's left so sum === grandTotal
                    $amount = $remaining;
                    $stage  = Payment::STAGE_FINAL;
                } else {
                    $stage = Payment::STAGE_ADVANCE;

                    // Budget: must leave enough for future payments.
                    // Future payments after this one: ($pmtCount - $p - 1)
                    // If no final payment, the last advance must leave at least 1 remaining,
                    // so add 1 to the "must leave" amount.
                    $futureCount = $pmtCount - $p - 1;
                    $mustLeave   = $hasFinal ? $futureCount : ($futureCount + 1);
                    $maxAmount   = $remaining - $mustLeave;

                    if ($maxAmount <= 0) {
                        break; // budget exhausted, stop adding payments
                    }

                    // Use 20–70% of available budget for natural variation
                    $amount = max(1, (int) ($maxAmount * fake()->randomFloat(2, 0.2, 0.7)));
                }

                Payment::create([
                    'invoice_id'   => $invoice->id,
                    'payment_type_id' => $paymentTypeIds->random(),
                    'stage'        => $stage,
                    'amount'       => $amount,
                    'note'         => fake()->optional(0.4)->sentence(),
                    'payment_date' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
                    'created_by'   => 1,
                ]);

                $remaining -= $amount;
            }
        }
    }
}
