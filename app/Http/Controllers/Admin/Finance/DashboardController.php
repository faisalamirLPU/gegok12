<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Helpers\SiteHelper;
use App\Http\Controllers\Controller;
use App\Models\Fee;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $schoolId = Auth::user()->school_id;

        $academicYear = SiteHelper::getAcademicYear($schoolId);

        /*
        |--------------------------------------------------------------------------
        | Finance Stats
        |--------------------------------------------------------------------------
        */

        $totalCollection = Fee::query()

            ->where('school_id', $schoolId)

            ->where('academic_year_id', $academicYear->id)

            ->sum('paid_amount');



        $pendingFees = Fee::query()

            ->where('school_id', $schoolId)

            ->where('academic_year_id', $academicYear->id)

            ->sum('balance');



        $overdueInvoices = Fee::query()

            ->where('school_id', $schoolId)

            ->where('academic_year_id', $academicYear->id)

            ->where('due_date', '<', now())

            ->where('balance', '>', 0)

            ->count();



        $todayCollection = Fee::query()

            ->where('school_id', $schoolId)

            ->where('academic_year_id', $academicYear->id)

            ->whereDate('created_at', today())

            ->sum('paid_amount');



        /*
        |--------------------------------------------------------------------------
        | Recent Transactions
        |--------------------------------------------------------------------------
        */

        $recentTransactions = Fee::query()

            ->with([
                'student',
                'studentAssignment.standardLink.standard',
                'studentAssignment.standardLink.section'
            ])

            ->where('school_id', $schoolId)

            ->where('academic_year_id', $academicYear->id)

            ->latest()

            ->take(10)

            ->get();



        return view(
            'admin.finance.dashboard.index',
            compact(
                'totalCollection',
                'pendingFees',
                'overdueInvoices',
                'todayCollection',
                'recentTransactions'
            )
        );
    }
}
