<?php

namespace App\Services\Finance;

use App\Models\Fee;
use App\Models\StudentFeeLedger;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class AdvanceCreditService
{
    public function getAdvanceBalance(int $schoolId, int $userId): float
    {
        return StudentFeeLedger::getAdvanceBalance($schoolId, $userId);
    }

    public function addCredit(
        int $schoolId,
        int $academicYearId,
        int $userId,
        float $amount,
        ?string $description = null,
        ?string $paymentPeriod = null
    ): StudentFeeLedger {
        return StudentFeeLedger::recordTransaction(
            $schoolId,
            $academicYearId,
            $userId,
            StudentFeeLedger::TYPE_ADVANCE_RECEIVED,
            $amount,
            'manual_credit',
            null,
            $description ?? 'Advance credit added',
            $paymentPeriod
        );
    }

    public function applyAdvanceToInvoice(Fee $invoice, ?float $amount = null): array
    {
        return DB::transaction(function () use ($invoice, $amount) {
            
            $availableAdvance = $this->getAdvanceBalance($invoice->school_id, $invoice->user_id);

            if ($availableAdvance <= 0) {
                return [
                    'applied' => 0,
                    'remaining' => 0,
                    'advance_used' => 0,
                ];
            }

            // Ensure we don't apply more than the balance due
            $balanceDue = (float) $invoice->balance;
            $amountToApply = $amount ?? min($availableAdvance, $balanceDue);

            if ($amountToApply <= 0) {
                return [
                    'applied' => 0,
                    'remaining' => $availableAdvance,
                    'advance_used' => 0,
                ];
            }

            // Record in ledger as advance applied
            StudentFeeLedger::recordTransaction(
                $invoice->school_id,
                $invoice->academic_year_id,
                $invoice->user_id,
                StudentFeeLedger::TYPE_ADVANCE_APPLIED,
                $amountToApply,
                'fee',
                $invoice->id,
                "Auto-applied advance to invoice {$invoice->invoice_no}",
                $invoice->payment_period
            );

            // Create a zero-amount payment record to track the application
            $receiptNo = 'RCPT-' . now()->format('YmdHis') . '-' . random_int(1000, 9999);
            while (Payment::where('receipt_no', $receiptNo)->exists()) {
                $receiptNo = 'RCPT-' . now()->format('YmdHis') . '-' . random_int(1000, 9999);
            }

            Payment::create([
                'school_id' => $invoice->school_id,
                'academic_year_id' => $invoice->academic_year_id,
                'fee_id' => $invoice->id,
                'user_id' => $invoice->user_id,
                'receipt_no' => $receiptNo,
                'amount' => 0,
                'payment_method' => 'advance',
                'remarks' => "Advance of Rs. {$amountToApply} auto-applied",
                'payment_date' => now(),
            ]);

            // Update fee totals
            $newPaidAmount = (float) $invoice->paid_amount + $amountToApply;
            $newBalance = max((float) $invoice->total_amount - $newPaidAmount, 0);
            $advanceAmount = max($newPaidAmount - (float) $invoice->total_amount, 0);

            $invoice->update([
                'paid_amount' => $newPaidAmount,
                'balance' => $newBalance,
                'advance_amount' => $advanceAmount,
            ]);

            $invoice->recalculateStatus();

            \App\Services\Audit\AuditTrailService::log(
                'advance applied',
                "Auto-applied advance {$amountToApply} to invoice {$invoice->invoice_no}",
                Fee::class,
                $invoice->id,
                ['paid_amount' => $invoice->paid_amount - $amountToApply, 'balance' => $invoice->balance + $amountToApply],
                ['paid_amount' => $newPaidAmount, 'balance' => $newBalance, 'applied' => $amountToApply]
            );

            return [
                'applied' => $newPaidAmount,
                'remaining' => $this->getAdvanceBalance($invoice->school_id, $invoice->user_id),
                'advance_used' => $amountToApply,
            ];
        });
    }

    public function carryForward(
        int $schoolId,
        int $academicYearId,
        int $userId,
        float $amount,
        ?string $fromPeriod = null,
        ?string $toPeriod = null
    ): StudentFeeLedger {
        return StudentFeeLedger::recordTransaction(
            $schoolId,
            $academicYearId,
            $userId,
            StudentFeeLedger::TYPE_CARRY_FORWARD,
            $amount,
            'carry_forward',
            null,
            "Carry forward from {$fromPeriod} to {$toPeriod}",
            $toPeriod
        );
    }

    public function getLedgerEntries(int $userId, ?string $period = null)
    {
        $query = StudentFeeLedger::forStudent($userId)
            ->with('student.userprofile')
            ->orderByDesc('id');

        if ($period) {
            $query->forPeriod($period);
        }

        return $query->get();
    }

    public function getMonthlyAdvanceSummary(int $schoolId, int $academicYearId, string $paymentPeriod): array
    {
        $students = \App\Models\User::where('school_id', $schoolId)
            ->where('usergroup_id', \App\Models\User::STUDENT_USERGROUP_ID)
            ->with('userprofile')
            ->get();

        $summary = [];
        foreach ($students as $student) {
            $balance = $this->getAdvanceBalance($schoolId, $student->id);
            if ($balance > 0) {
                $summary[] = [
                    'student' => $student,
                    'advance_balance' => $balance,
                ];
            }
        }

        return $summary;
    }
}