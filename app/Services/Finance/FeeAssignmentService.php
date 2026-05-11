<?php

namespace App\Services\Finance;

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
            | Get Standard Links
            |--------------------------------------------------------------------------
            */

            $standardLinks = StandardLink::query()

                ->where(
                    'school_id',
                    $structure->school_id
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
            | Stop if no standard links found
            |--------------------------------------------------------------------------
            */

            if ($standardLinks->isEmpty()) {
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Get Students
            |--------------------------------------------------------------------------
            */

            $students = StudentAcademic::query()

                ->where(
                    'school_id',
                    $structure->school_id
                )

                ->whereIn(
                    'standardLink_id',
                    $standardLinks->pluck('id')
                )

                ->get();

            /*
            |--------------------------------------------------------------------------
            | Stop if no students found
            |--------------------------------------------------------------------------
            */

            if ($students->isEmpty()) {
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Calculate Total Amount
            |--------------------------------------------------------------------------
            */

            $totalAmount = $structure->items->sum('amount');

            /*
            |--------------------------------------------------------------------------
            | Create Student Assignments
            |--------------------------------------------------------------------------
            */

            foreach ($students as $student) {

                StudentFeeAssignment::updateOrCreate(

                    [
                        'school_id' => $structure->school_id,

                        'academic_year_id' => $structure->academic_year_id,

                        'user_id' => $student->user_id,

                        'fee_id' => $structure->id,
                    ],

                    [
                        'standard_link_id' => $student->standardLink_id,

                        'assigned_amount' => $totalAmount,

                        'paid_amount' => 0,

                        'balance' => $totalAmount,

                        'assigned_on' => now(),

                        'due_date' => now()->addMonth(),

                        'status' => 0,
                    ]
                );
            }
        });
    }
}
