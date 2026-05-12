<?php

namespace App\Services\Finance;

use App\Models\Fee;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class FeePaymentService
{
    public function collectPayment(
        Fee $fee,
        array $data
    ): Payment {

        return DB::transaction(function () use ($fee, $data) {

            /*
            |----------------------------------------------------------------------
            | Generate Receipt Number
            |----------------------------------------------------------------------
            */

            $receiptNo = 'RCPT-' .
                now()->format('Ymd') .
                '-' .
                rand(1000, 9999);

            /*
            |----------------------------------------------------------------------
            | Create Payment
            |----------------------------------------------------------------------
            */

            $payment = Payment::create([

                'school_id' => $fee->school_id,

                'academic_year_id' => $fee->academic_year_id,

                'fee_id' => $fee->id,

                'user_id' => $fee->user_id,

                'receipt_no' => $receiptNo,

                'amount' => $data['amount'],

                'payment_method' => $data['payment_method'],

                'transaction_id' => $data['transaction_id'] ?? null,

                'remarks' => $data['remarks'] ?? null,

                'payment_date' => now(),
            ]);

            /*
            |----------------------------------------------------------------------
            | Update Fee Invoice
            |----------------------------------------------------------------------
            */

            $newPaidAmount =
                $fee->paid_amount + $data['amount'];

            $newBalance = max(
                (float) $fee->total_amount - (float) $newPaidAmount,
                0
            );

            /*
            |----------------------------------------------------------------------
            | Determine Status
            |----------------------------------------------------------------------
            */

            $status = 0;

            if ((float) $newPaidAmount > (float) $fee->total_amount) {

                $status = 3;
            } elseif ((float) $newPaidAmount >= (float) $fee->total_amount) {

                $status = 2;
            } elseif ((float) $newPaidAmount > 0) {

                $status = 1;
            }

            /*
            |----------------------------------------------------------------------
            | Update Fee
            |----------------------------------------------------------------------
            */

            $fee->update([

                'paid_amount' => $newPaidAmount,

                'balance' => $newBalance,

                'status' => $status,
            ]);

            return $payment;
        });
    }
}
