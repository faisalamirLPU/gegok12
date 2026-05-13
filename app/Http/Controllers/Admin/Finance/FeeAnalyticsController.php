<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Helpers\SiteHelper;
use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Models\FeeCategory;
use App\Models\StudentSpecialFee;
use App\Models\User;
use App\Services\Finance\AdvanceCreditService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FeeAnalyticsController extends Controller
{
    protected AdvanceCreditService $advanceCreditService;

    public function __construct(AdvanceCreditService $advanceCreditService)
    {
        $this->advanceCreditService = $advanceCreditService;
    }

    /**
     * Analytics Dashboard
     */
    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $academicYear = SiteHelper::getAcademicYear($schoolId);

        // Get the selected month/year or default to current
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $paymentPeriod = "{$year}-" . str_pad($month, 2, '0', STR_PAD_LEFT);

        // Overview Stats for the academic year
        $yearlyStats = $this->getYearlyStats($schoolId, $academicYear->id);

        // Monthly Stats for selected period
        $monthlyStats = $this->getMonthlyStats($schoolId, $academicYear->id, $paymentPeriod);

        // Collection by month (for chart)
        $monthlyCollection = $this->getMonthlyCollection($schoolId, $academicYear->id);

        // Category Breakdown
        $categoryBreakdown = $this->getCategoryBreakdown($schoolId, $academicYear->id);

        // Defaulters List
        $defaulters = $this->getDefaultersList($schoolId, $academicYear->id, 5);

        // Special Fee Stats
        $specialFeeStats = $this->getSpecialFeeStats($schoolId, $academicYear->id);

        // Top Paying Students
        $topPayingStudents = $this->getTopPayingStudents($schoolId, $academicYear->id, 10);

        // Recent Transactions
        $recentTransactions = $this->getRecentTransactions($schoolId, $academicYear->id, 10);

        return view('admin.finance.fee-records.analytics', compact(
            'yearlyStats',
            'monthlyStats',
            'monthlyCollection',
            'categoryBreakdown',
            'defaulters',
            'specialFeeStats',
            'topPayingStudents',
            'recentTransactions',
            'academicYear',
            'month',
            'year',
            'paymentPeriod'
        ));
    }

    /**
     * Get yearly statistics for the academic year
     */
    protected function getYearlyStats(int $schoolId, int $academicYearId): array
    {
        $fees = Fee::where('school_id', $schoolId)
            ->where('academic_year_id', $academicYearId);

        return [
            'total_demand' => (clone $fees)->sum('total_amount'),
            'total_collected' => (clone $fees)->sum('paid_amount'),
            'total_pending' => (clone $fees)->sum('balance'),
            'total_invoices' => (clone $fees)->count(),
            'paid_invoices' => (clone $fees)->whereColumn('paid_amount', '>=', 'total_amount')->where('total_amount', '>', 0)->count(),
            'partial_invoices' => (clone $fees)->where('paid_amount', '>', 0)->whereColumn('paid_amount', '<', 'total_amount')->count(),
            'pending_invoices' => (clone $fees)->where('paid_amount', '<=', 0)->count(),
            'advance_amount' => (clone $fees)->selectRaw('COALESCE(SUM(CASE WHEN paid_amount > total_amount THEN paid_amount - total_amount ELSE 0 END), 0) as total')->value('total'),
        ];
    }

    /**
     * Get monthly statistics for a specific payment period
     */
    protected function getMonthlyStats(int $schoolId, int $academicYearId, string $paymentPeriod): array
    {
        $fees = Fee::where('school_id', $schoolId)
            ->where('academic_year_id', $academicYearId)
            ->where('payment_period', $paymentPeriod);

        $totalStudents = User::where('school_id', $schoolId)
            ->where('usergroup_id', User::STUDENT_USERGROUP_ID)
            ->where('status', 1)
            ->whereHas('studentAcademic', fn($q) => $q->where('academic_year_id', $academicYearId))
            ->count();

        return [
            'total_demand' => (clone $fees)->sum('total_amount'),
            'total_collected' => (clone $fees)->sum('paid_amount'),
            'total_pending' => (clone $fees)->sum('balance'),
            'invoice_count' => (clone $fees)->count(),
            'paid_count' => (clone $fees)->whereColumn('paid_amount', '>=', 'total_amount')->where('total_amount', '>', 0)->count(),
            'total_students' => $totalStudents,
            'collection_rate' => $totalStudents > 0 ? round((clone $fees)->whereColumn('paid_amount', '>=', 'total_amount')->where('total_amount', '>', 0)->count() / $totalStudents * 100, 1) : 0,
        ];
    }

    /**
     * Get monthly collection data for chart
     */
    protected function getMonthlyCollection(int $schoolId, int $academicYearId): array
    {
        $collections = Fee::where('school_id', $schoolId)
            ->where('academic_year_id', $academicYearId)
            ->whereNotNull('payment_period')
            ->selectRaw('payment_period,
                        SUM(total_amount) as demand,
                        SUM(paid_amount) as collected,
                        SUM(balance) as pending')
            ->groupBy('payment_period')
            ->orderBy('payment_period')
            ->get();

        return $collections->map(function ($item) {
            $period = Carbon::createFromFormat('Y-m', $item->payment_period);
            return [
                'month' => $period->format('M Y'),
                'month_short' => $period->format('M'),
                'demand' => (float) $item->demand,
                'collected' => (float) $item->collected,
                'pending' => (float) $item->pending,
                'rate' => $item->demand > 0 ? round(($item->collected / $item->demand) * 100, 1) : 0,
            ];
        })->toArray();
    }

    /**
     * Get fee category breakdown
     */
    protected function getCategoryBreakdown(int $schoolId, int $academicYearId): array
    {
        $categories = FeeCategory::where('school_id', $schoolId)
            ->where('academic_year_id', $academicYearId)
            ->where('status', 1)
            ->with(['feeItems' => function ($q) use ($schoolId, $academicYearId) {
                $q->whereHas('fee', fn($fee) => $fee->where('school_id', $schoolId)->where('academic_year_id', $academicYearId));
            }])
            ->get();

        return $categories->map(function ($category) {
            $totalAmount = $category->feeItems->sum('total');
            return [
                'id' => $category->id,
                'name' => $category->name,
                'code' => $category->code,
                'is_optional' => $category->is_optional,
                'total_amount' => $totalAmount,
            ];
        })->filter(fn($c) => $c['total_amount'] > 0)->values()->toArray();
    }

    /**
     * Get defaulters list (students with pending fees)
     */
    protected function getDefaultersList(int $schoolId, int $academicYearId, int $limit = 20): array
    {
        $defaulterFees = Fee::where('school_id', $schoolId)
            ->where('academic_year_id', $academicYearId)
            ->where('balance', '>', 0)
            ->whereColumn('paid_amount', '<', 'total_amount')
            ->orderBy('balance', 'desc')
            ->limit($limit)
            ->with(['student.userprofile', 'studentAcademic.standardLink.standard', 'studentAcademic.standardLink.section'])
            ->get();

        return $defaulterFees->map(function ($fee) {
            $profile = $fee->student?->userprofile;
            return [
                'student_id' => $fee->user_id,
                'student_name' => trim(($profile->firstname ?? '') . ' ' . ($profile->lastname ?? '')) ?: ($fee->student?->name ?? 'N/A'),
                'class' => optional(optional($fee->studentAcademic)->standardLink)->standard?->name ?? '',
                'section' => optional(optional($fee->studentAcademic)->standardLink)->section?->name ?? '',
                'invoice_no' => $fee->invoice_no,
                'period' => $fee->display_period ?? $fee->payment_period,
                'total' => (float) $fee->total_amount,
                'paid' => (float) $fee->paid_amount,
                'balance' => (float) $fee->balance_amount,
                'days_overdue' => $fee->due_date ? now()->diffInDays($fee->due_date) : 0,
            ];
        })->toArray();
    }

    /**
     * Get special fee statistics
     */
    protected function getSpecialFeeStats(int $schoolId, int $academicYearId): array
    {
        $specialFees = StudentSpecialFee::where('school_id', $schoolId)
            ->where('academic_year_id', $academicYearId);

        return [
            'total_count' => (clone $specialFees)->count(),
            'active_count' => (clone $specialFees)->where('status', 1)->count(),
            'applied_count' => (clone $specialFees)->where('status', 2)->count(),
            'total_amount' => (clone $specialFees)->sum('amount'),
        ];
    }

    /**
     * Get top paying students
     */
    protected function getTopPayingStudents(int $schoolId, int $academicYearId, int $limit = 10): array
    {
        $students = Fee::where('school_id', $schoolId)
            ->where('academic_year_id', $academicYearId)
            ->selectRaw('user_id, SUM(paid_amount) as total_paid')
            ->groupBy('user_id')
            ->orderByDesc('total_paid')
            ->limit($limit)
            ->with(['student.userprofile'])
            ->get();

        return $students->map(function ($item, $index) {
            $profile = $item->student?->userprofile;
            return [
                'rank' => $index + 1,
                'student_id' => $item->user_id,
                'student_name' => trim(($profile->firstname ?? '') . ' ' . ($profile->lastname ?? '')) ?: ($item->student?->name ?? 'N/A'),
                'total_paid' => (float) $item->total_paid,
            ];
        })->toArray();
    }

    /**
     * Get recent transactions
     */
    protected function getRecentTransactions(int $schoolId, int $academicYearId, int $limit = 10): array
    {
        // Get recent fee updates/payments
        $fees = Fee::where('school_id', $schoolId)
            ->where('academic_year_id', $academicYearId)
            ->where('paid_amount', '>', 0)
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->with(['student.userprofile'])
            ->get();

        return $fees->map(function ($fee) {
            $profile = $fee->student?->userprofile;
            return [
                'date' => $fee->updated_at,
                'type' => 'payment',
                'student_name' => trim(($profile->firstname ?? '') . ' ' . ($profile->lastname ?? '')) ?: ($fee->student?->name ?? 'N/A'),
                'invoice_no' => $fee->invoice_no,
                'amount' => (float) $fee->paid_amount,
                'description' => "Payment received - " . ($fee->display_period ?? $fee->payment_period),
            ];
        })->toArray();
    }
}