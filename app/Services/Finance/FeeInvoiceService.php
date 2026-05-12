<?php

namespace App\Services\Finance;

use App\Models\Fee;
use App\Models\FeeItem;
use App\Models\FeeStructure;
use App\Models\StudentFeeAssignment;
use App\Models\StudentSpecialFee;
use App\Models\StudentAcademic;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FeeInvoiceService
{
    public function generateFromAssignment(
        StudentFeeAssignment $assignment
    ): Fee {

        return DB::transaction(function () use ($assignment) {

            $structure = FeeStructure::with('items.feeCategory')
                ->findOrFail($assignment->fee_id);

            $studentAcademic = $this->studentAcademicForAssignment($assignment);
            $dueDate = $this->resolveDueDate($structure, $assignment->due_date);
            $billingCycle = $this->billingCycle($dueDate);

            $fee = $this->findOrCreatePendingInvoice(
                $assignment,
                $studentAcademic,
                $dueDate,
                $billingCycle
            );

            foreach ($structure->items as $item) {
                $this->mergeInvoiceItem(
                    $fee,
                    (int) $item->fee_category_id,
                    (float) $item->amount
                );
            }

            return $this->refreshInvoiceTotals($fee);
        });
    }

    public function appendSpecialFee(StudentSpecialFee $specialFee): Fee
    {
        return DB::transaction(function () use ($specialFee) {
            $studentAcademic = StudentAcademic::query()
                ->where('school_id', $specialFee->school_id)
                ->where('academic_year_id', $specialFee->academic_year_id)
                ->where('user_id', $specialFee->user_id)
                ->latest('id')
                ->first();

            $dueDate = $specialFee->due_date ?: now();
            $billingCycle = $this->billingCycle($dueDate);

            $assignment = StudentFeeAssignment::firstOrCreate(
                [
                    'school_id' => $specialFee->school_id,
                    'academic_year_id' => $specialFee->academic_year_id,
                    'user_id' => $specialFee->user_id,
                    'standard_link_id' => $studentAcademic?->standardLink_id,
                    'fee_id' => null,
                ],
                [
                    'assigned_amount' => 0,
                    'paid_amount' => 0,
                    'balance' => 0,
                    'assigned_on' => now(),
                    'due_date' => $dueDate,
                    'status' => 0,
                ]
            );

            $fee = $this->findOrCreatePendingInvoice(
                $assignment,
                $studentAcademic,
                $dueDate,
                $billingCycle
            );

            $this->mergeInvoiceItem(
                $fee,
                (int) $specialFee->fee_category_id,
                (float) $specialFee->amount
            );

            return $this->refreshInvoiceTotals($fee);
        });
    }

    private function findOrCreatePendingInvoice(
        StudentFeeAssignment $assignment,
        ?StudentAcademic $studentAcademic,
        mixed $dueDate,
        string $billingCycle
    ): Fee {
        $fee = Fee::query()
            ->where('school_id', $assignment->school_id)
            ->where('academic_year_id', $assignment->academic_year_id)
            ->where('user_id', $assignment->user_id)
            ->where('billing_cycle', $billingCycle)
            ->whereIn('status', [0, 1])
            ->lockForUpdate()
            ->first();

        if ($fee) {
            $existingDueDate = Carbon::parse($fee->due_date);
            $incomingDueDate = Carbon::parse($dueDate);

            $fee->update([
                'student_academic_id' => $fee->student_academic_id ?: $studentAcademic?->id,
                'due_date' => $existingDueDate->lessThanOrEqualTo($incomingDueDate)
                    ? $existingDueDate->toDateString()
                    : $incomingDueDate->toDateString(),
            ]);

            return $fee;
        }

        return Fee::create([
            'school_id' => $assignment->school_id,
            'academic_year_id' => $assignment->academic_year_id,
            'student_fee_assignment_id' => $assignment->id,
            'student_academic_id' => $studentAcademic?->id,
            'user_id' => $assignment->user_id,
            'invoice_no' => $this->invoiceNumber($assignment->user_id),
            'billing_cycle' => $billingCycle,
            'total_amount' => 0,
            'paid_amount' => 0,
            'balance' => 0,
            'due_date' => Carbon::parse($dueDate)->toDateString(),
            'generated_on' => now(),
            'status' => 0,
        ]);
    }

    private function mergeInvoiceItem(
        Fee $fee,
        int $feeCategoryId,
        float $amount
    ): FeeItem {
        $item = FeeItem::firstOrNew([
            'fee_id' => $fee->id,
            'fee_category_id' => $feeCategoryId,
        ]);

        $item->amount = (float) ($item->amount ?? 0) + $amount;
        $item->fine_amount = (float) ($item->fine_amount ?? 0);
        $item->total = (float) $item->amount + (float) $item->fine_amount;
        $item->save();

        return $item;
    }

    private function refreshInvoiceTotals(Fee $fee): Fee
    {
        $total = (float) $fee->items()->sum('total');
        $paid = (float) $fee->paid_amount;
        $balance = max($total - $paid, 0);

        $status = 0;

        if ($paid > $total) {
            $status = 3;
        } elseif ($paid >= $total && $total > 0) {
            $status = 2;
        } elseif ($paid > 0) {
            $status = 1;
        }

        $fee->update([
            'total_amount' => $total,
            'balance' => $balance,
            'status' => $status,
        ]);

        return $fee->fresh(['items.category']);
    }

    private function studentAcademicForAssignment(
        StudentFeeAssignment $assignment
    ): ?StudentAcademic {
        return StudentAcademic::query()
            ->where('school_id', $assignment->school_id)
            ->where('academic_year_id', $assignment->academic_year_id)
            ->where('user_id', $assignment->user_id)
            ->where('standardLink_id', $assignment->standard_link_id)
            ->latest('id')
            ->first();
    }

    private function resolveDueDate(
        FeeStructure $structure,
        mixed $fallback
    ): Carbon {
        if ($fallback) {
            return Carbon::parse($fallback);
        }

        $day = $structure->due_day
            ? min((int) $structure->due_day, 28)
            : now()->addMonth()->day;

        return now()->copy()->addMonthNoOverflow()->day($day);
    }

    private function billingCycle(mixed $dueDate): string
    {
        return Carbon::parse($dueDate)->format('Y-m');
    }

    private function invoiceNumber(int $userId): string
    {
        do {
            $invoiceNo = 'INV-'
                . now()->format('YmdHis')
                . '-'
                . str_pad((string) $userId, 4, '0', STR_PAD_LEFT)
                . '-'
                . random_int(100, 999);
        } while (Fee::where('invoice_no', $invoiceNo)->exists());

        return $invoiceNo;
    }
}
