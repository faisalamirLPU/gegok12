<?php

namespace App\Services\Finance;

use App\Models\Fee;
use App\Models\FeeStructure;
use App\Models\StudentAcademic;
use App\Models\StudentFeeAssignment;
use App\Models\StandardLink;
use Illuminate\Support\Facades\DB;

class FeeAssignmentService
{
    public function __construct(
        protected FeeInvoiceService $feeInvoiceService
    ) {
    }

    public function assignStructureToStudents(
        FeeStructure $structure
    ): void {

        DB::transaction(function () use ($structure) {
            $structure->loadMissing('items.feeCategory');

            /*
            |--------------------------------------------------------------------------
            | Fetch Standard Links
            |--------------------------------------------------------------------------
            */

            $standardLinks = StandardLink::query()

                ->where(
                    'school_id',
                    $structure->school_id
                )

                ->where(
                    'academic_year_id',
                    $structure->academic_year_id
                )

                ->where(
                    'standard_id',
                    $structure->class_id
                )

                ->when(
                    $structure->section_id,
                    function ($query) use ($structure) {

                        $query->where(
                            'section_id',
                            $structure->section_id
                        );
                    }
                )

                ->get();

            /*
            |--------------------------------------------------------------------------
            | Fetch Students
            |--------------------------------------------------------------------------
            */

            $students = StudentAcademic::query()

                ->where(
                    'school_id',
                    $structure->school_id
                )

                ->where(
                    'academic_year_id',
                    $structure->academic_year_id
                )

                ->whereIn(
                    'standardLink_id',
                    $standardLinks->pluck('id')
                )

                ->get();

            /*
            |--------------------------------------------------------------------------
            | Stop if no students
            |--------------------------------------------------------------------------
            */

            if ($students->count() === 0) {
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Calculate Total Amount
            |--------------------------------------------------------------------------
            */

            $totalAmount =
                $structure->items->sum('amount');

            /*
            |--------------------------------------------------------------------------
            | Create Assignments + Invoices
            |--------------------------------------------------------------------------
            */

            foreach ($students as $student) {

                /*
                |--------------------------------------------------------------------------
                | Student Assignment
                |--------------------------------------------------------------------------
                */

                $assignment = StudentFeeAssignment::firstOrNew(

                    [

                        'school_id' => $structure->school_id,

                        'academic_year_id' => $structure->academic_year_id,

                        'user_id' => $student->user_id,

                        'standard_link_id' => $student->standardLink_id,

                        'fee_id' => $structure->id,
                    ],
                );

                $assignment->fill([
                    'assigned_amount' => $totalAmount,
                    'paid_amount' => $assignment->exists
                        ? $assignment->paid_amount
                        : 0,
                    'balance' => max(
                        $totalAmount - (float) ($assignment->paid_amount ?? 0),
                        0
                    ),
                    'assigned_on' => $assignment->assigned_on ?: now(),
                    'due_date' => $assignment->due_date ?: now()->addMonth(),
                    'status' => (float) ($assignment->paid_amount ?? 0) > 0 ? 1 : 0,
                ]);

                $assignment->save();

                /*
                |--------------------------------------------------------------------------
                | Create Or Merge Invoice
                |--------------------------------------------------------------------------
                */

                $this->feeInvoiceService->generateFromAssignment($assignment);

                /*
                |--------------------------------------------------------------------------
                | Link Fee Structure
                |--------------------------------------------------------------------------
                */

                $assignment->update(['fee_id' => $structure->id]);
            }
        });
    }
}
