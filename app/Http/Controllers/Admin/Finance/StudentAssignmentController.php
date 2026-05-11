<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\StudentFeeAssignment;

class StudentAssignmentController extends Controller
{
    public function index()
    {
        $assignments = StudentFeeAssignment::query()

            ->with([
                'student',
                'feeStructure'
            ])

            ->latest()

            ->paginate(20);

        return view(
            'admin.finance.student-assignments.index',
            compact('assignments')
        );
    }
}
