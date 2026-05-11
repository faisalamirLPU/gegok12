<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Helpers\SiteHelper;
use App\Http\Controllers\Controller;
use App\Models\StudentFeeAssignment;

class StudentAssignmentController extends Controller
{
    public function index()
    {
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
                SiteHelper::getAcademicYear(
                    auth()->user()->school_id
                )->id
            )

            ->latest()

            ->paginate(20);

        return view(
            'admin.finance.student-assignments.index',
            compact('assignments')
        );
    }
}
