<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Helpers\SiteHelper;
use App\Http\Controllers\Controller;
use App\Models\StudentFeeAssignment;

class StudentAssignmentController extends Controller
{
    public function index()
    {
        $academicYear = SiteHelper::getAcademicYear(
            auth()->user()->school_id
        );

        $assignments = StudentFeeAssignment::query()

            ->with([

                'student.userprofile',

                'feeStructure',

                'standardLink.standard',

                'standardLink.section',
            ])

            ->where(
                'school_id',
                auth()->user()->school_id
            )

            ->where(
                'academic_year_id',
                $academicYear->id
            )

            ->latest()

            ->paginate(20);

        return view(
            'admin.finance.student-assignments.index',
            compact('assignments')
        );
    }
}
