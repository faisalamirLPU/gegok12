<?php

namespace App\Services\Finance;

use App\Models\Fee;
use App\Models\FeeStructure;
use App\Models\FeeStructureItem;
use App\Models\StudentFeeAssignment;
use Illuminate\Support\Facades\DB;
use App\Services\Audit\AuditTrailService;
use App\Services\Finance\FeeAssignmentService;
use App\Models\StandardLink;

class FeeStructureService
{
    protected \App\Services\Finance\MonthlyInvoiceGeneratorService $invoiceGenerator;

    public function __construct(
        FeeAssignmentService $feeAssignmentService,
        \App\Services\Finance\MonthlyInvoiceGeneratorService $invoiceGenerator
    ) {
        $this->feeAssignmentService = $feeAssignmentService;
        $this->invoiceGenerator = $invoiceGenerator;
    }

    public function create(array $data): FeeStructure
    {
        return DB::transaction(function () use ($data) {
            $structure = FeeStructure::create([
                'school_id'        => $data['school_id'],
                'academic_year_id' => $data['academic_year_id'],
                'class_id'         => $data['class_id'],
                'section_id'       => $data['section_id'] ?? null,
                'title'            => $data['title'],
                'description'      => $data['description'] ?? null,
                'installment_type' => $data['installment_type'],
                'due_type'         => $data['due_type'],
                'due_day'          => $data['due_day'] ?? null,
                'status'           => true,
            ]);

            foreach ($data['items'] as $item) {
                FeeStructureItem::create([
                    'fee_structure_id' => $structure->id,
                    'fee_category_id'  => $item['fee_category_id'],
                    'fine_rule_id'     => $item['fine_rule_id'] ?? null,
                    'amount'           => $item['amount'],
                    'due_date'         => $item['due_date'] ?? null,
                    'is_optional'      => $item['is_optional'] ?? false,
                    'sort_order'       => $item['sort_order'] ?? 0,
                ]);
            }

            $structure->load('items');

            /*
            |----------------------------------------------------------------------
            | Assign Structure To Students
            |----------------------------------------------------------------------
            */
            $this->feeAssignmentService->assignStructureToStudents($structure);

            /*
            |----------------------------------------------------------------------
            | ERP Finance Automation: Generate Invoices for all students in this class
            | Using the unified monthly generator to ensure consistency and prevent empty records
            |----------------------------------------------------------------------
            */
            $standardLink = StandardLink::where('standard_id', $structure->class_id)
                ->when($structure->section_id, fn($q) => $q->where('section_id', $structure->section_id))
                ->where('school_id', $structure->school_id)
                ->where('academic_year_id', $structure->academic_year_id)
                ->get();

            foreach ($standardLink as $link) {
                $this->invoiceGenerator->generateClassInvoices(
                    $structure->school_id,
                    $structure->academic_year_id,
                    $link->id,
                    now()
                );
            }

            return $structure;
        });
    }

    public function delete(FeeStructure $structure): array
    {
        $structure->fill([
            'status' => false,
            'is_archived' => true,
        ])->save();

        $structure->items()->delete();

        if (! $structure->trashed()) {
            $structure->delete();
        }

        AuditTrailService::log(
            'archive_fee_structure',
            sprintf('Archived fee structure #%s.', $structure->id),
            FeeStructure::class,
            $structure->id,
            null,
            $structure->toArray()
        );

        return ['message' => 'Fee structure archived successfully.'];
    }

    public function restore(int $id): FeeStructure
    {
        $structure = FeeStructure::withTrashed()->findOrFail($id);
        $structure->restore();
        $structure->fill([
            'status' => true,
            'is_archived' => false,
        ])->save();
        $structure->items()->withTrashed()->restore();

        AuditTrailService::log(
            'restore_fee_structure',
            sprintf('Restored fee structure #%s.', $structure->id),
            FeeStructure::class,
            $structure->id,
            null,
            $structure->toArray()
        );

        return $structure;
    }
}
