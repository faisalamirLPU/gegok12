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
    public function assignStructureToStudents(
        FeeStructure $structure
    ): void {

        DB::transaction(function () use ($structure) {

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

                $assignment = StudentFeeAssignment::updateOrCreate(

                    [

                        'school_id' => $structure->school_id,

                        'academic_year_id' => $structure->academic_year_id,

                        'user_id' => $student->user_id,

                        'standard_link_id' => $student->standardLink_id,
                    ],

                    [

                        'assigned_amount' => $totalAmount,

                        'paid_amount' => 0,

                        'balance' => $totalAmount,

                        'assigned_on' => now(),

                        'due_date' => now()->addMonth(),

                        'status' => 0,
                    ]
                );

                /*
                |--------------------------------------------------------------------------
                | Create Invoice
                |--------------------------------------------------------------------------
                */

                $invoice = Fee::updateOrCreate(

                    [

                        'school_id' => $structure->school_id,

                        'academic_year_id' => $structure->academic_year_id,

                        'user_id' => $student->user_id,

                        'student_fee_assignment_id' => $assignment->id,
                    ],

                    [

                        'student_academic_id' => $student->id,

                        'invoice_no' => 'INV-'
                            . now()->format('Ymd')
                            . '-'
                            . str_pad(
                                $student->user_id,
                                4,
                                '0',
                                STR_PAD_LEFT
                            ),

                        'total_amount' => $totalAmount,

                        'paid_amount' => 0,

                        'balance' => $totalAmount,

                        'due_date' => now()->addMonth(),

                        'status' => 0,
                    ]
                );

                /*
                |--------------------------------------------------------------------------
                | Link Fee Structure
                |--------------------------------------------------------------------------
                */

                $assignment->update([

                    'fee_id' => $structure->id
                ]);
            }
        });
    }
}
