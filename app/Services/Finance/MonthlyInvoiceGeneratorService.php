<?php

namespace App\Services\Finance;

use App\Models\Fee;
use App\Models\FeeItem;
use App\Models\FeeStructure;
use App\Models\FeeStructureItem;
use App\Models\StudentFeeAssignment;
use App\Models\StudentSpecialFee;
use App\Models\StudentAcademic;
use App\Helpers\SiteHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MonthlyInvoiceGeneratorService
{
    protected AdvanceCreditService $advanceCreditService;

    public function __construct(AdvanceCreditService $advanceCreditService)
    {
        $this->advanceCreditService = $advanceCreditService;
    }

    /**
     * Generate or update monthly invoice for a single student
     */
    public function generateMonthlyInvoice(
        int $schoolId,
        int $academicYearId,
        int $userId,
        Carbon $forMonth,
        ?int $classId = null
    ): ?Fee {
        return DB::transaction(function () use ($schoolId, $academicYearId, $userId, $forMonth, $classId) {
            $paymentPeriod = $forMonth->format('Y-m');
            $month = (int) $forMonth->format('m');
            $year = (int) $forMonth->format('Y');

            // Check if invoice already exists for this period
            $existingInvoice = Fee::where('school_id', $schoolId)
                ->where('academic_year_id', $academicYearId)
                ->where('user_id', $userId)
                ->where('payment_period', $paymentPeriod)
                ->first();

            if ($existingInvoice && $existingInvoice->is_locked) {
                return $existingInvoice; // Don't modify locked invoices
            }

            // Get student's academic record
            $studentAcademic = StudentAcademic::query()
                ->where('school_id', $schoolId)
                ->where('academic_year_id', $academicYearId)
                ->where('user_id', $userId)
                ->when($classId, fn($q) => $q->where('standardLink_id', $classId))
                ->latest('id')
                ->first();

            if ($existingInvoice) {
                return $this->refreshInvoice($existingInvoice, $studentAcademic);
            }

            // Create new invoice
            return $this->createMonthlyInvoice(
                $schoolId,
                $academicYearId,
                $userId,
                $studentAcademic,
                $paymentPeriod,
                $month,
                $year
            );
        });
    }

    /**
     * Generate invoices for all students in a class for a specific month
     */
    public function generateClassInvoices(
        int $schoolId,
        int $academicYearId,
        int $classId,
        Carbon $forMonth
    ): array {
        $students = \App\Models\User::where('school_id', $schoolId)
            ->where('usergroup_id', \App\Models\User::STUDENT_USERGROUP_ID)
            ->whereHas('studentAcademic', function ($q) use ($classId, $academicYearId) {
                $q->where('standardLink_id', $classId)
                  ->where('academic_year_id', $academicYearId);
            })
            ->get();

        $results = [
            'generated' => 0,
            'skipped' => 0,
            'errors' => [],
        ];

        foreach ($students as $student) {
            try {
                $invoice = $this->generateMonthlyInvoice(
                    $schoolId,
                    $academicYearId,
                    $student->id,
                    $forMonth,
                    $classId
                );

                if ($invoice) {
                    $results['generated']++;
                }
            } catch (\Exception $e) {
                $results['errors'][] = [
                    'student_id' => $student->id,
                    'error' => $e->getMessage(),
                ];
                $results['skipped']++;
            }
        }

        return $results;
    }

    /**
     * Generate invoices for all active students for a month
     */
    public function generateAllStudentInvoices(
        int $schoolId,
        int $academicYearId,
        Carbon $forMonth
    ): array {
        $students = \App\Models\User::where('school_id', $schoolId)
            ->where('usergroup_id', \App\Models\User::STUDENT_USERGROUP_ID)
            ->where('status', 1)
            ->whereHas('studentAcademic', function ($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId);
            })
            ->get();

        $results = [
            'generated' => 0,
            'skipped' => 0,
            'errors' => [],
        ];

        foreach ($students as $student) {
            try {
                $invoice = $this->generateMonthlyInvoice(
                    $schoolId,
                    $academicYearId,
                    $student->id,
                    $forMonth
                );

                if ($invoice) {
                    $results['generated']++;
                }
            } catch (\Exception $e) {
                $results['errors'][] = [
                    'student_id' => $student->id,
                    'error' => $e->getMessage(),
                ];
                $results['skipped']++;
            }
        }

        return $results;
    }

    /**
     * Create a new monthly invoice with all applicable fees
     */
    protected function createMonthlyInvoice(
        int $schoolId,
        int $academicYearId,
        int $userId,
        ?StudentAcademic $studentAcademic,
        string $paymentPeriod,
        int $month,
        int $year
    ): Fee {
        $dueDate = Carbon::createFromDate($year, $month, 1)->addDays(10)->endOfMonth();
        $invoiceNo = $this->generateInvoiceNumber($schoolId);

        $invoice = Fee::create([
            'school_id' => $schoolId,
            'academic_year_id' => $academicYearId,
            'student_academic_id' => $studentAcademic?->id,
            'user_id' => $userId,
            'invoice_no' => $invoiceNo,
            'billing_cycle' => $paymentPeriod,
            'payment_period' => $paymentPeriod,
            'month' => $month,
            'year' => $year,
            'total_amount' => 0,
            'paid_amount' => 0,
            'balance' => 0,
            'due_date' => $dueDate,
            'generated_on' => now(),
            'status' => Fee::STATUS_PENDING,
            'is_locked' => false,
        ]);

        // Add structural fees
        $this->addStructuralFees($invoice, $studentAcademic);

        // Add special fees
        $this->addSpecialFees($invoice);

        // Refresh totals
        return $invoice->recalculateTotals();
    }

    /**
     * Add structural fees from fee structures assigned to the student
     */
    protected function addStructuralFees(Fee $invoice, ?StudentAcademic $studentAcademic): void
    {
        if (!$studentAcademic) {
            return;
        }

        $standardLinkId = $studentAcademic->standardLink_id;

        // Get active fee structures for this class/section
        $structures = FeeStructure::query()
            ->where('school_id', $invoice->school_id)
            ->where('academic_year_id', $invoice->academic_year_id)
            ->where(function ($q) use ($standardLinkId) {
                $q->where('class_id', $standardLinkId)
                  ->orWhereNull('class_id');
            })
            ->active()
            ->with('items.feeCategory')
            ->get();

        foreach ($structures as $structure) {
            // Check if this structure should apply to this month
            if (!$this->structureAppliesToMonth($structure, $invoice->month)) {
                continue;
            }

            foreach ($structure->items as $item) {
                if (!$item->feeCategory || $item->feeCategory->status != 1) {
                    continue;
                }

                // Check if category is optional and not selected
                if ($item->feeCategory->is_optional && !$this->isCategorySelected($invoice->user_id, $item->fee_category_id)) {
                    continue;
                }

                $this->mergeInvoiceItem($invoice, $item->fee_category_id, (float) $item->amount);
            }
        }
    }

    /**
     * Add special one-time fees assigned to the student
     */
    protected function addSpecialFees(Fee $invoice): void
    {
        $specialFees = StudentSpecialFee::query()
            ->where('school_id', $invoice->school_id)
            ->where('academic_year_id', $invoice->academic_year_id)
            ->where('user_id', $invoice->user_id)
            ->where('status', 1)
            ->with('feeCategory')
            ->get();

        foreach ($specialFees as $specialFee) {
            if (!$specialFee->feeCategory || $specialFee->feeCategory->status != 1) {
                continue;
            }

            $this->mergeInvoiceItem($invoice, $specialFee->fee_category_id, (float) $specialFee->amount);

            // Mark special fee as processed for this period
            $specialFee->update(['status' => 2]); // 2 = applied to invoice
        }
    }

    /**
     * Check if a fee structure applies to a specific month
     */
    protected function structureAppliesToMonth(FeeStructure $structure, int $month): bool
    {
        $installmentType = $structure->installment_type ?? 'monthly';

        return match ($installmentType) {
            'monthly' => true,
            'quarterly' => in_array($month, [3, 6, 9, 12]),
            'half_yearly' => in_array($month, [6, 12]),
            'annual' => $month == 4 || $month == 1,
            default => true,
        };
    }

    /**
     * Check if student has selected an optional fee category
     */
    protected function isCategorySelected(int $userId, int $categoryId): bool
    {
        return \App\Models\StudentOptionalFee::where('user_id', $userId)
            ->where('fee_category_id', $categoryId)
            ->exists();
    }

    /**
     * Merge an item into existing invoice or create new one
     */
    protected function mergeInvoiceItem(Fee $invoice, int $feeCategoryId, float $amount): FeeItem
    {
        $item = FeeItem::firstOrNew([
            'fee_id' => $invoice->id,
            'fee_category_id' => $feeCategoryId,
        ]);

        $item->amount = (float) ($item->amount ?? 0) + $amount;
        $item->fine_amount = (float) ($item->fine_amount ?? 0);
        $item->total = (float) $item->amount + (float) $item->fine_amount;
        $item->save();

        return $item;
    }

    /**
     * Refresh an existing invoice with latest fee items
     */
    protected function refreshInvoice(Fee $invoice, ?StudentAcademic $studentAcademic): Fee
    {
        // Re-add structural fees (will merge with existing items)
        $this->addStructuralFees($invoice, $studentAcademic);

        // Refresh totals
        return $invoice->recalculateTotals();
    }

    /**
     * Generate a unique invoice number
     */
    protected function generateInvoiceNumber(int $schoolId): string
    {
        $prefix = 'INV';
        $date = now()->format('Ymd');
        $random = str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);

        $invoiceNo = "{$prefix}-{$date}-{$random}";

        // Ensure uniqueness
        while (Fee::where('invoice_no', $invoiceNo)->exists()) {
            $random = str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
            $invoiceNo = "{$prefix}-{$date}-{$random}";
        }

        return $invoiceNo;
    }
}