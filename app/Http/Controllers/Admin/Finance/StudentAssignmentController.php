<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Helpers\SiteHelper;
use App\Http\Controllers\Controller;
use App\Models\StudentFeeAssignment;
use Illuminate\Http\Request;

class StudentAssignmentController extends Controller
{
    public function index(Request $request)
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

            ->when(
                $request->search,
                function ($query, $search) {

                    $query->whereHas(
                        'student.userprofile',
                        function ($q) use ($search) {

                            $q->where(
                                'firstname',
                                'like',
                                "%{$search}%"
                            )

                            ->orWhere(
                                'lastname',
                                'like',
                                "%{$search}%"
                            );
                        }
                    );
                }
            )

            ->latest()

            ->paginate(20)

            ->withQueryString();

        return view(
            'admin.finance.student-assignments.index',
            compact('assignments')
        );
    }
}
