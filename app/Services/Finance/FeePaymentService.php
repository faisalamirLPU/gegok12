<?php

namespace App\Services\Finance;

use App\Models\Fee;
use App\Models\Payment;
use App\Models\StudentFeeLedger;
use Illuminate\Support\Facades\DB;

class FeePaymentService
{
    protected AdvanceCreditService $advanceCreditService;

    public function __construct(
        AdvanceCreditService $advanceCreditService
    ) {
        $this->advanceCreditService = $advanceCreditService;
    }

    /**
     * Collect payment for invoice
     */
    public function collectPayment(
        Fee $fee,
        array $data
    ): Payment {

        return DB::transaction(function () use ($fee, $data) {

            $fee->refresh();

            $amount =
                (float) $data['amount'];

            $paymentMethod =
                $data['payment_method'] ?? 'cash';

            $transactionId =
                $data['transaction_id'] ?? null;

            $remarks =
                $data['remarks'] ?? null;

            $paymentDate =
                $data['payment_date'] ?? now();

            /*
            |--------------------------------------------------------------------------
            | Locked Invoice Protection
            |--------------------------------------------------------------------------
            */

            if ($fee->is_locked) {

                throw new \Exception(
                    'This invoice is locked and cannot be modified.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Create Payment Entry
            |--------------------------------------------------------------------------
            */

            $receiptNo =
                $this->generateReceiptNumber();

            $payment = Payment::create([

                'school_id' =>
                    $fee->school_id,

                'academic_year_id' =>
                    $fee->academic_year_id,

                'fee_id' =>
                    $fee->id,

                'user_id' =>
                    $fee->user_id,

                'receipt_no' =>
                    $receiptNo,

                'amount' =>
                    $amount,

                'payment_method' =>
                    $paymentMethod,

                'transaction_id' =>
                    $transactionId,

                'remarks' =>
                    $remarks,

                'payment_date' =>
                    $paymentDate,
            ]);

            /*
            |--------------------------------------------------------------------------
            | ERP Payment Logic
            |--------------------------------------------------------------------------
            */

            $currentPaid =
                (float) $fee->paid_amount;

            $totalAmount =
                (float) $fee->total_amount;

            $newPaidAmount =
                $currentPaid + $amount;

            /*
            |--------------------------------------------------------------------------
            | Calculate Balance & Advance
            |--------------------------------------------------------------------------
            */

            $balance =
                max($totalAmount - $newPaidAmount, 0);

            $advance =
                max($newPaidAmount - $totalAmount, 0);

            /*
            |--------------------------------------------------------------------------
            | Update Invoice ONCE ONLY
            |--------------------------------------------------------------------------
            */

            $fee->update([

                'paid_amount' =>
                    $newPaidAmount,

                'balance' =>
                    $balance,

                'advance_amount' =>
                    $advance,

            ]);

            $fee->recalculateStatus();

            /*
            |--------------------------------------------------------------------------
            | Record Advance Ledger
            |--------------------------------------------------------------------------
            */

            if ($advance > 0) {

                StudentFeeLedger::recordTransaction(

                    $fee->school_id,

                    $fee->academic_year_id,

                    $fee->user_id,

                    StudentFeeLedger::TYPE_ADVANCE_RECEIVED,

                    $advance,

                    'payment',

                    $payment->id,

                    "Advance received from invoice {$fee->invoice_no}",

                    $fee->payment_period
                );
            }

            \App\Services\Audit\AuditTrailService::log(
                'payment collected',
                "Collected {$amount} via {$paymentMethod} for invoice {$fee->invoice_no}",
                Payment::class,
                $payment->id,
                ['paid_amount' => $currentPaid],
                ['paid_amount' => $newPaidAmount, 'method' => $paymentMethod, 'amount' => $amount]
            );

            return $payment;
        });
    }

    /**
     * Apply advance balance to invoice
     */
    public function applyAdvancePayment(
        Fee $fee,
        float $amount,
        ?string $remarks = null
    ): array {

        return DB::transaction(function () use ($fee, $amount, $remarks) {

            if ($fee->is_locked) {

                throw new \Exception(
                    'This invoice is locked and cannot be modified.'
                );
            }

            $availableAdvance =
                $this->advanceCreditService
                    ->getAdvanceBalance(
                        $fee->school_id,
                        $fee->user_id
                    );

            $amountToApply =
                min(
                    $amount,
                    $availableAdvance,
                    (float) $fee->balance
                );

            if ($amountToApply <= 0) {

                return [

                    'success' => false,

                    'message' =>
                        'No advance balance available.',

                    'applied' => 0,
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | Advance Ledger
            |--------------------------------------------------------------------------
            */

            StudentFeeLedger::recordTransaction(

                $fee->school_id,

                $fee->academic_year_id,

                $fee->user_id,

                StudentFeeLedger::TYPE_ADVANCE_APPLIED,

                $amountToApply,

                'fee',

                $fee->id,

                $remarks
                ?? "Advance applied to invoice {$fee->invoice_no}",

                $fee->payment_period
            );

            /*
            |--------------------------------------------------------------------------
            | Payment Entry
            |--------------------------------------------------------------------------
            */

            Payment::create([

                'school_id' =>
                    $fee->school_id,

                'academic_year_id' =>
                    $fee->academic_year_id,

                'fee_id' =>
                    $fee->id,

                'user_id' =>
                    $fee->user_id,

                'receipt_no' =>
                    $this->generateReceiptNumber(),

                'amount' =>
                    0,

                'payment_method' =>
                    'advance',

                'remarks' =>
                    $remarks
                    ?? "Advance of Rs. {$amountToApply} applied",

                'payment_date' =>
                    now(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Update Invoice
            |--------------------------------------------------------------------------
            */

            $newPaid =
                (float) $fee->paid_amount + $amountToApply;

            $balance =
                max(
                    (float) $fee->total_amount - $newPaid,
                    0
                );

            $fee->update([

                'paid_amount' =>
                    $newPaid,

                'balance' =>
                    $balance,

            ]);

            $fee->recalculateStatus();

            \App\Services\Audit\AuditTrailService::log(
                'advance applied',
                "Applied advance {$amountToApply} to invoice {$fee->invoice_no}",
                Fee::class,
                $fee->id,
                ['paid_amount' => $fee->paid_amount - $amountToApply, 'balance' => $fee->balance + $amountToApply],
                ['paid_amount' => $newPaid, 'balance' => $balance, 'applied' => $amountToApply]
            );

            return [

                'success' => true,

                'message' =>
                    "Advance of Rs. {$amountToApply} applied successfully.",

                'applied' =>
                    $amountToApply,

                'remaining_advance' =>
                    $this->advanceCreditService
                        ->getAdvanceBalance(
                            $fee->school_id,
                            $fee->user_id
                        ),
            ];
        });
    }

    /**
     * Generate Receipt Number
     */
    protected function generateReceiptNumber(): string
    {
        do {

            $receiptNo =
                'RCPT-' .
                now()->format('YmdHis') .
                '-' .
                random_int(1000, 9999);

        } while (
            Payment::where(
                'receipt_no',
                $receiptNo
            )->exists()
        );

        return $receiptNo;
    }

    /**
     * Payment History
     */
    public function getPaymentHistory(Fee $fee)
    {
        return Payment::where(
            'fee_id',
            $fee->id
        )
            ->latest()
            ->get();
    }

    /**
     * Reverse Payment
     */
    public function reversePayment(
        Payment $payment,
        string $reason
    ): bool {

        return DB::transaction(function () use ($payment, $reason) {

            $fee =
                $payment->fee;

            if ($fee->is_locked) {

                throw new \Exception(
                    'Invoice locked.'
                );
            }

            $newPaid =
                max(
                    0,
                    (float) $fee->paid_amount
                    - (float) $payment->amount
                );

            $balance =
                max(
                    (float) $fee->total_amount
                    - $newPaid,
                    0
                );

            $advance =
                max(
                    $newPaid
                    - (float) $fee->total_amount,
                    0
                );

            $fee->update([

                'paid_amount' =>
                    $newPaid,

                'balance' =>
                    $balance,

                'advance_amount' =>
                    $advance,

            ]);

            $fee->recalculateStatus();

            $payment->update([

                'remarks' =>
                    $payment->remarks .
                    " [REVERSED: {$reason}]",
            ]);

            \App\Services\Audit\AuditTrailService::log(
                'payment reversed',
                "Reversed payment {$payment->receipt_no} for invoice {$fee->invoice_no}. Reason: {$reason}",
                Payment::class,
                $payment->id,
                ['status' => 'active', 'fee_paid' => $fee->paid_amount + $payment->amount],
                ['status' => 'reversed', 'fee_paid' => $newPaid]
            );

            return true;
        });
    }
}