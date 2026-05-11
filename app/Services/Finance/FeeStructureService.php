<?php

namespace App\Services\Finance;

use App\Models\FeeStructure;
use App\Models\FeeStructureItem;
use App\Models\StudentFeeAssignment;
use Illuminate\Support\Facades\DB;
use App\Services\Finance\FeeAssignmentService;
use App\Services\Finance\FeeInvoiceService;
// use App\Models\StudentFeeAssignment;

class FeeStructureService
{
    protected FeeAssignmentService $feeAssignmentService;

    protected FeeInvoiceService $feeInvoiceService;

    public function __construct(
        FeeAssignmentService $feeAssignmentService,
        FeeInvoiceService $feeInvoiceService
    ) {
        $this->feeAssignmentService = $feeAssignmentService;

        $this->feeInvoiceService = $feeInvoiceService;
    }

    public function create(array $data): FeeStructure
    {
        return DB::transaction(function () use ($data) {

            /*
            |----------------------------------------------------------------------
            | Create Fee Structure
            |----------------------------------------------------------------------
            */

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

            /*
            |----------------------------------------------------------------------
            | Create Structure Items
            |----------------------------------------------------------------------
            */

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

            /*
            |----------------------------------------------------------------------
            | Load Items
            |----------------------------------------------------------------------
            */

            $structure->load('items');

            /*
            |----------------------------------------------------------------------
            | Assign Structure To Students
            |----------------------------------------------------------------------
            */

            $this->feeAssignmentService
                ->assignStructureToStudents($structure);

            /*
            |----------------------------------------------------------------------
            | Generate Student Invoices
            |----------------------------------------------------------------------
            */

            $assignments = StudentFeeAssignment::query()

                ->where('fee_id', $structure->id)

                ->get();

            foreach ($assignments as $assignment) {

                $this->feeInvoiceService
                    ->generateFromAssignment($assignment);
            }

            return $structure;
        });
    }
}
