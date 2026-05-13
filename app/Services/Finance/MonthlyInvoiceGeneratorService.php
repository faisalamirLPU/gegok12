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
            // Standardized Duplicate Prevention: Only check school, year, user, and period
            $existingInvoice = Fee::where('school_id', (int) $schoolId)
                ->where('academic_year_id', (int) $academicYearId)
                ->where('user_id', (int) $userId)
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
        $results = [
            'generated' => 0,
            'skipped' => 0,
            'errors' => [],
        ];

        \App\Models\User::where('school_id', $schoolId)
            ->where('usergroup_id', \App\Models\User::STUDENT_USERGROUP_ID)
            ->where('status', 1)
            ->whereHas('studentAcademic', function ($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId);
            })
            ->chunk(100, function ($students) use (&$results, $schoolId, $academicYearId, $forMonth) {
                foreach ($students as $student) {
                    try {
                        $invoice = $this->generateMonthlyInvoice(
                            (int) $schoolId,
                            (int) $academicYearId,
                            (int) $student->id,
                            $forMonth
                        );

                        if ($invoice) {
                            $results['generated']++;
                        } else {
                            $results['skipped']++;
                            $results['errors'][] = [
                                'student_id' => $student->id,
                                'reason' => 'Empty invoice or no applicable fees',
                            ];
                        }
                    } catch (\Exception $e) {
                        $results['errors'][] = [
                            'student_id' => $student->id,
                            'error' => $e->getMessage(),
                            'trace' => substr($e->getTraceAsString(), 0, 200),
                        ];
                        $results['skipped']++;
                        \Log::error("ERP Invoice Skip [Student: {$student->id}]: " . $e->getMessage());
                    }
                }
            });

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
    ): ?Fee {
        // Wrap everything in a sub-transaction to allow rollback if empty
        return DB::transaction(function () use ($schoolId, $academicYearId, $userId, $studentAcademic, $paymentPeriod, $month, $year) {
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

            // Recalculate totals
            $invoice->recalculateTotals();

            // CRITICAL: Prevent generating invoices with 0 amount or no items
            if ($invoice->total_amount <= 0 || $invoice->items()->count() === 0) {
                // Rollback this specific creation by throwing an exception or returning null
                // Returning null is better if handled by the caller
                DB::rollBack();
                return null;
            }

            // AUTOMATION: Automatically apply available advance credits
            $this->advanceCreditService->applyAdvanceToFee($invoice);

            return $invoice->fresh(['items.category', 'payments']);
        });
    }

    protected function addStructuralFees(Fee $invoice, ?StudentAcademic $studentAcademic): void
    {
        if (!$studentAcademic || !$studentAcademic->standardLink) {
            return;
        }

        // Use standard_id and section_id from the link
        $standardId = $studentAcademic->standardLink->standard_id;
        $sectionId = $studentAcademic->standardLink->section_id;

        // Get active fee structures for this specific class and section
        $structures = FeeStructure::query()
            ->where('school_id', $invoice->school_id)
            ->where('academic_year_id', $invoice->academic_year_id)
            ->where(function ($q) use ($standardId, $sectionId) {
                $q->where('class_id', $standardId)
                    ->where(function ($sq) use ($sectionId) {
                        $sq->whereNull('section_id')
                            ->orWhere('section_id', $sectionId);
                    });
            })
            ->active()
            ->with([
                'items' => function ($q) {
                    $q->where('status', 1)->orWhereNull('status'); // Ensure items are active if applicable
                },
                'items.feeCategory'
            ])
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

                $this->mergeInvoiceItem(
                    $invoice,
                    (int) $item->fee_category_id,
                    (float) $item->amount,
                    'structure',
                    (int) $item->id,
                    $structure->title
                );
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

            $this->mergeInvoiceItem(
                $invoice,
                (int) $specialFee->fee_category_id,
                (float) $specialFee->amount,
                'special',
                (int) $specialFee->id,
                $specialFee->remarks
            );

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
     * Logic: Standardize on source_type and source_id to prevent duplicate merging
     */
    protected function mergeInvoiceItem(
        Fee $invoice,
        int $feeCategoryId,
        float $amount,
        ?string $sourceType = null,
        ?int $sourceId = null,
        ?string $remarks = null
    ): FeeItem {
        // Unique check includes source to allow separate rows for same category
        $item = FeeItem::firstOrNew([
            'fee_id' => $invoice->id,
            'fee_category_id' => $feeCategoryId,
            'source_type' => $sourceType,
            'source_id' => $sourceId,
        ]);

        $item->amount = (float) $amount; // Overwrite or set initial
        $item->fine_amount = (float) ($item->fine_amount ?? 0);
        $item->total = (float) $item->amount + (float) $item->fine_amount;
        $item->remarks = $remarks;
        $item->save();

        \Log::info("ERP Invoice Item Created/Updated: [Inv: {$invoice->id}] [Cat: {$feeCategoryId}] [Amount: {$amount}] [Source: {$sourceType} ID: {$sourceId}]");

        return $item;
    }

    /**
     * Refresh an existing invoice with latest fee items
     */
    protected function refreshInvoice(Fee $invoice, ?StudentAcademic $studentAcademic): Fee
    {
        // Re-add structural fees
        $this->addStructuralFees($invoice, $studentAcademic);

        // Add any pending special fees
        $this->addSpecialFees($invoice);

        // Refresh totals
        return $invoice->recalculateTotals();
    }

    /**
     * Generate a unique invoice number in standardized format: INV-YYYYMM-XXXX
     */
    protected function generateInvoiceNumber(int $schoolId): string
    {
        $prefix = 'INV';
        $period = now()->format('Ym');

        // Get the latest sequence for this school and period
        $latest = Fee::where('school_id', $schoolId)
            ->where('invoice_no', 'like', "{$prefix}-{$period}-%")
            ->orderByDesc('invoice_no')
            ->first();

        if ($latest) {
            $parts = explode('-', $latest->invoice_no);
            $sequence = (int) end($parts) + 1;
        } else {
            $sequence = 1;
        }

        $invoiceNo = "{$prefix}-{$period}-" . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);

        // Safety check for uniqueness
        while (Fee::where('invoice_no', $invoiceNo)->exists()) {
            $sequence++;
            $invoiceNo = "{$prefix}-{$period}-" . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
        }

        return $invoiceNo;
    }
}