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
            StudentFeeLedger::TYPE_ADVANCEReceived,
            $amount,
            'manual_credit',
            null,
            $description ?? 'Advance credit added',
            $paymentPeriod
        );
    }

    public function applyAdvanceToFee(Fee $fee, ?float $amount = null): array
    {
        return DB::transaction(function () use ($fee, $amount) {
            $availableAdvance = $this->getAdvanceBalance($fee->school_id, $fee->user_id);

            if ($availableAdvance <= 0) {
                return [
                    'applied' => 0,
                    'remaining' => 0,
                    'advance_used' => 0,
                ];
            }

            $balanceDue = (float) $fee->balance;
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
                $fee->school_id,
                $fee->academic_year_id,
                $fee->user_id,
                StudentFeeLedger::TYPE_ADVANCE_APPLIED,
                $amountToApply,
                'fee',
                $fee->id,
                "Advance applied to invoice {$fee->invoice_no}",
                $fee->payment_period
            );

            // Update fee
            $newPaidAmount = (float) $fee->paid_amount + $amountToApply;
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

            return [
                'applied' => $newPaidAmount,
                'remaining' => $this->getAdvanceBalance($fee->school_id, $fee->user_id),
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