<?php

namespace App\Services\Finance;

use App\Models\Fee;
use App\Models\Payment;
use App\Models\StudentFeeLedger;
use Illuminate\Support\Facades\DB;

class FeePaymentService
{
    protected AdvanceCreditService $advanceCreditService;

    public function __construct(AdvanceCreditService $advanceCreditService)
    {
        $this->advanceCreditService = $advanceCreditService;
    }

    /**
     * Collect payment for a fee invoice with proper advance handling
     */
    public function collectPayment(Fee $fee, array $data): Payment
    {
        return DB::transaction(function () use ($fee, $data) {
            $amount = (float) $data['amount'];
            $paymentMethod = $data['payment_method'] ?? 'cash';
            $transactionId = $data['transaction_id'] ?? null;
            $remarks = $data['remarks'] ?? null;
            $paymentDate = $data['payment_date'] ?? now();

            // Handle locked invoice
            if ($fee->is_locked) {
                throw new \Exception('This invoice is locked and cannot be modified.');
            }

            // Calculate what needs to be paid
            $balance = (float) $fee->balance;
            $availableAdvance = $this->advanceCreditService->getAdvanceBalance($fee->school_id, $fee->user_id);
            $totalPayable = $balance - $availableAdvance;

            // If amount exceeds balance after advance, record as advance
            if ($amount > $totalPayable && $totalPayable > 0) {
                // Apply advance first
                $this->advanceCreditService->applyAdvanceToFee($fee, $availableAdvance);
                $remainingBalance = (float) $fee->fresh()->balance;
            } else {
                $remainingBalance = $balance;
            }

            // Calculate actual payment and any excess (advance)
            $excessAmount = max(0, $amount - $remainingBalance);

            // Create payment record
            $receiptNo = $this->generateReceiptNumber();
            $payment = Payment::create([
                'school_id' => $fee->school_id,
                'academic_year_id' => $fee->academic_year_id,
                'fee_id' => $fee->id,
                'user_id' => $fee->user_id,
                'receipt_no' => $receiptNo,
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'transaction_id' => $transactionId,
                'remarks' => $remarks,
                'payment_date' => $paymentDate,
            ]);

            // Record advance credit if any excess
            if ($excessAmount > 0) {
                StudentFeeLedger::recordTransaction(
                    $fee->school_id,
                    $fee->academic_year_id,
                    $fee->user_id,
                    StudentFeeLedger::TYPE_ADVANCEReceived,
                    $excessAmount,
                    'payment',
                    $payment->id,
                    "Advance received from payment for invoice {$fee->invoice_no}",
                    $fee->payment_period
                );
            }

            // Update fee totals
            $this->updateFeeTotals($fee, $amount, $excessAmount);

            return $payment;
        });
    }

    /**
     * Record a payment using advance credit (no cash received)
     */
    public function applyAdvancePayment(Fee $fee, float $amount, ?string $remarks = null): array
    {
        return DB::transaction(function () use ($fee, $amount, $remarks) {
            if ($fee->is_locked) {
                throw new \Exception('This invoice is locked and cannot be modified.');
            }

            $availableAdvance = $this->advanceCreditService->getAdvanceBalance($fee->school_id, $fee->user_id);
            $amountToApply = min($amount, $availableAdvance, (float) $fee->balance);

            if ($amountToApply <= 0) {
                return [
                    'success' => false,
                    'message' => 'No advance balance available to apply.',
                    'applied' => 0,
                ];
            }

            // Record the advance application
            StudentFeeLedger::recordTransaction(
                $fee->school_id,
                $fee->academic_year_id,
                $fee->user_id,
                StudentFeeLedger::TYPE_ADVANCE_APPLIED,
                $amountToApply,
                'fee',
                $fee->id,
                $remarks ?? "Advance applied to invoice {$fee->invoice_no}",
                $fee->payment_period
            );

            // Create a zero-amount payment record to track the application
            $receiptNo = $this->generateReceiptNumber();
            Payment::create([
                'school_id' => $fee->school_id,
                'academic_year_id' => $fee->academic_year_id,
                'fee_id' => $fee->id,
                'user_id' => $fee->user_id,
                'receipt_no' => $receiptNo,
                'amount' => 0,
                'payment_method' => 'advance',
                'remarks' => $remarks ?? "Advance of Rs. {$amountToApply} applied",
                'payment_date' => now(),
            ]);

            // Update fee totals
            $this->updateFeeTotals($fee, $amountToApply, 0);

            return [
                'success' => true,
                'message' => "Advance of Rs. {$amountToApply} applied successfully.",
                'applied' => $amountToApply,
                'remaining_advance' => $this->advanceCreditService->getAdvanceBalance($fee->school_id, $fee->user_id),
            ];
        });
    }

    /**
     * Record a bulk/manual adjustment
     */
    public function recordAdjustment(Fee $fee, float $amount, string $description, string $type = 'waiver'): Payment
    {
        return DB::transaction(function () use ($fee, $amount, $description, $type) {
            if ($fee->is_locked) {
                throw new \Exception('This invoice is locked and cannot be modified.');
            }

            $receiptNo = $this->generateReceiptNumber();
            $paymentMethod = $type === 'refund' ? 'refund' : 'adjustment';

            // For waivers/reductions, amount should be negative for accounting
            $adjustedAmount = $type === 'waiver' ? -abs($amount) : abs($amount);

            $payment = Payment::create([
                'school_id' => $fee->school_id,
                'academic_year_id' => $fee->academic_year_id,
                'fee_id' => $fee->id,
                'user_id' => $fee->user_id,
                'receipt_no' => $receiptNo,
                'amount' => $adjustedAmount,
                'payment_method' => $paymentMethod,
                'remarks' => $description,
                'payment_date' => now(),
            ]);

            // Update fee totals
            $fee->total_amount = max(0, (float) $fee->total_amount + $adjustedAmount);
            $fee->balance = max(0, (float) $fee->total_amount - (float) $fee->paid_amount);
            $fee->save();

            return $payment;
        });
    }

    /**
     * Update fee totals after payment
     */
    protected function updateFeeTotals(Fee $fee, float $paymentAmount, float $excessAmount): Fee
    {
        $newPaidAmount = (float) $fee->paid_amount + $paymentAmount + $excessAmount;
        $newBalance = max((float) $fee->total_amount - $newPaidAmount, 0);

        $status = Fee::STATUS_PENDING;
        if ($newPaidAmount > $fee->total_amount) {
            $status = Fee::STATUS_ADVANCE;
        } elseif ($newPaidAmount >= $fee->total_amount && $fee->total_amount > 0) {
            $status = Fee::STATUS_PAID;
        } elseif ($newPaidAmount > 0) {
            $status = Fee::STATUS_PARTIAL;
        }

        $fee->update([
            'paid_amount' => $newPaidAmount,
            'balance' => $newBalance,
            'status' => $status,
        ]);

        return $fee->fresh();
    }

    /**
     * Generate unique receipt number
     */
    protected function generateReceiptNumber(): string
    {
        $receiptNo = 'RCPT-' . now()->format('Ymd') . '-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);

        while (Payment::where('receipt_no', $receiptNo)->exists()) {
            $receiptNo = 'RCPT-' . now()->format('Ymd') . '-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        }

        return $receiptNo;
    }

    /**
     * Get payment history for a fee
     */
    public function getPaymentHistory(Fee $fee): \Illuminate\Database\Eloquent\Collection
    {
        return Payment::where('fee_id', $fee->id)
            ->orderByDesc('payment_date')
            ->get();
    }

    /**
     * Reverse a payment (admin action)
     */
    public function reversePayment(Payment $payment, string $reason): bool
    {
        return DB::transaction(function () use ($payment, $reason) {
            $fee = $payment->fee;

            if ($fee->is_locked) {
                throw new \Exception('This invoice is locked. Unlock it first to reverse payments.');
            }

            // Reverse the amount
            $fee->paid_amount = max(0, (float) $fee->paid_amount - (float) $payment->amount);
            $fee->balance = max(0, (float) $fee->total_amount - (float) $fee->paid_amount);

            // Recalculate status
            if ($fee->paid_amount > $fee->total_amount) {
                $fee->status = Fee::STATUS_ADVANCE;
            } elseif ($fee->paid_amount >= $fee->total_amount && $fee->total_amount > 0) {
                $fee->status = Fee::STATUS_PAID;
            } elseif ($fee->paid_amount > 0) {
                $fee->status = Fee::STATUS_PARTIAL;
            } else {
                $fee->status = Fee::STATUS_PENDING;
            }

            $fee->save();

            // Mark payment as reversed
            $payment->update([
                'remarks' => $payment->remarks . " [REVERSED: {$reason}]",
                'payment_date' => $payment->payment_date,
            ]);

            return true;
        });
    }
}
