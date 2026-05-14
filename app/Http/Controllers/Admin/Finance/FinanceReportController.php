<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Services\Finance\FinanceReportService;
use App\Models\SchoolDetail;
use App\Helpers\SiteHelper;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FinanceReportController extends Controller
{
    protected FinanceReportService $reportService;

    public function __construct(FinanceReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function collection(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $academicYearId = SiteHelper::getAcademicYear($schoolId)->id;
        $date = $request->get('date', now()->toDateString());

        $dailyCollection = $this->reportService->getDailyCollection($schoolId, $date);
        $monthlyCollection = $this->reportService->getMonthlyCollection($schoolId, $academicYearId);

        return view('admin.finance.reports.collection', compact('dailyCollection', 'monthlyCollection', 'date'));
    }

    public function outstanding(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $academicYearId = SiteHelper::getAcademicYear($schoolId)->id;

        $filters = $request->only(['class_id', 'section_id', 'due_date_from', 'due_date_to']);
        
        $report = $this->reportService->getOutstandingDues($schoolId, $academicYearId, $filters);

        return view('admin.finance.reports.outstanding', compact('report', 'filters'));
    }

    public function advanceBalances(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $records = $this->reportService->getAdvanceBalances($schoolId);

        return view('admin.finance.reports.advance-balances', compact('records'));
    }

    public function aging(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $academicYearId = SiteHelper::getAcademicYear($schoolId)->id;

        $aging = $this->reportService->getAgingReport($schoolId, $academicYearId);

        return view('admin.finance.reports.aging', compact('aging'));
    }
}
