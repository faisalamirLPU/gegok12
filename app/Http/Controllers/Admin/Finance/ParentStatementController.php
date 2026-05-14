<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Services\Finance\ParentStatementService;
use App\Models\User;
use App\Models\SchoolDetail;
use Illuminate\Http\Request;
use PDF; // Assuming barryvdh/laravel-dompdf is installed as used in FeePaymentController

class ParentStatementController extends Controller
{
    protected ParentStatementService $statementService;

    public function __construct(ParentStatementService $statementService)
    {
        $this->statementService = $statementService;
    }

    public function show(Request $request, User $student)
    {
        $schoolId = auth()->user()->school_id;
        $academicYearId = auth()->user()->academic_year_id;

        if ($student->school_id != $schoolId || $student->usergroup_id != User::STUDENT_USERGROUP_ID) {
            abort(404);
        }

        $statement = $this->statementService->getStudentStatement($schoolId, $academicYearId, $student->id);

        return view('admin.finance.statements.show', compact('statement', 'student'));
    }

    public function pdf(Request $request, User $student)
    {
        $schoolId = auth()->user()->school_id;
        $academicYearId = auth()->user()->academic_year_id;

        if ($student->school_id != $schoolId || $student->usergroup_id != User::STUDENT_USERGROUP_ID) {
            abort(404);
        }

        $statement = $this->statementService->getStudentStatement($schoolId, $academicYearId, $student->id);
        $school = auth()->user()->school;

        $pdf = PDF::loadView('admin.finance.statements.pdf', compact('statement', 'student', 'school'));
        return $pdf->stream("Statement_{$student->id}.pdf");
    }
}
