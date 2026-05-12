<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Helpers\SiteHelper;
use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Models\FeeCategory;
use App\Models\FeeStructure;
use App\Models\StudentSpecialFee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeeManagementController extends Controller
{
    /**
     * Display the unified fee management dashboard
     */
    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;
        $academicYear = SiteHelper::getAcademicYear($schoolId);

        $invoiceQuery = Fee::where('school_id', $schoolId)
            ->where('academic_year_id', $academicYear->id);

        $stats = [
            'total_collection' => (clone $invoiceQuery)->sum('paid_amount'),
            'pending_collection' => (clone $invoiceQuery)->sum('balance'),
            'advance_collection' => (clone $invoiceQuery)
                ->selectRaw('COALESCE(SUM(CASE WHEN paid_amount > total_amount THEN paid_amount - total_amount ELSE 0 END), 0) as total')
                ->value('total'),
            'total_invoices' => (clone $invoiceQuery)->count(),
            'partial_payments' => (clone $invoiceQuery)
                ->where('paid_amount', '>', 0)
                ->whereColumn('paid_amount', '<', 'total_amount')
                ->count(),
            'paid_invoices' => (clone $invoiceQuery)
                ->whereColumn('paid_amount', '>=', 'total_amount')
                ->whereColumn('paid_amount', '<=', 'total_amount')
                ->count(),
            'total_categories' => FeeCategory::where('school_id', $schoolId)
                ->where('academic_year_id', $academicYear->id)
                ->count(),
            'active_categories' => FeeCategory::where('school_id', $schoolId)
                ->where('academic_year_id', $academicYear->id)
                ->where('status', 1)
                ->count(),
            'total_structures' => FeeStructure::where('school_id', $schoolId)
                ->where('academic_year_id', $academicYear->id)
                ->count(),
            'total_special_fees' => StudentSpecialFee::where('school_id', $schoolId)
                ->where('academic_year_id', $academicYear->id)
                ->count(),
            'pending_payments' => (clone $invoiceQuery)->where('balance', '>', 0)->count(),
            'collected_amount' => (clone $invoiceQuery)->sum('paid_amount'),
            'pending_amount' => (clone $invoiceQuery)->sum('balance'),
        ];

        $recentFees = Fee::with([
                'student.userprofile',
                'studentAcademic.standardLink.standard',
                'studentAcademic.standardLink.section',
                'items.category',
            ])
            ->where('school_id', $schoolId)
            ->where('academic_year_id', $academicYear->id)
            ->latest()
            ->limit(5)
            ->get();

        $recentSpecialFees = StudentSpecialFee::with(['student.userprofile', 'feeCategory'])
            ->where('school_id', $schoolId)
            ->where('academic_year_id', $academicYear->id)
            ->latest()
            ->limit(5)
            ->get();

        return view(
            'admin.finance.fee-management.index',
            compact('stats', 'recentFees', 'recentSpecialFees', 'academicYear')
        );
    }

    /**
     * Show all fee categories
     */
    public function categories()
    {
        $feeCategories = FeeCategory::query()
            ->currentSchool()
            ->currentAcademicYear()
            ->latest()
            ->paginate(20);

        return view(
            'admin.finance.fee-management.categories',
            compact('feeCategories')
        );
    }

    /**
     * Show all fee structures
     */
    public function structures()
    {
        $structures = FeeStructure::query()
            ->with(['items.feeCategory'])
            ->currentSchool()
            ->currentAcademicYear()
            ->latest()
            ->paginate(20);

        return view(
            'admin.finance.fee-management.structures',
            compact('structures')
        );
    }

    /**
     * Show all special fees
     */
    public function specialFees()
    {
        $specialFees = StudentSpecialFee::with([
            'student.userprofile',
            'feeCategory'
        ])
        ->where('school_id', Auth::user()->school_id)
        ->where('academic_year_id', SiteHelper::getAcademicYear(Auth::user()->school_id)->id)
        ->latest()
        ->paginate(20);

        return view(
            'admin.finance.fee-management.special-fees',
            compact('specialFees')
        );
    }

    /**
     * Show all fee payments
     */
    public function payments(Request $request)
    {
        $schoolId = Auth::user()->school_id;
        $academicYear = SiteHelper::getAcademicYear($schoolId);

        $fees = Fee::query()
            ->with([
                'student.userprofile',
                'studentAcademic.standardLink.standard',
                'studentAcademic.standardLink.section',
                'items.category',
            ])
            ->where('school_id', $schoolId)
            ->where('academic_year_id', $academicYear->id)
            ->when(
                $request->search,
                function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('invoice_no', 'like', "%{$search}%")
                            ->orWhereHas('student.userprofile', function ($student) use ($search) {
                                $student->where('firstname', 'like', "%{$search}%")
                                    ->orWhere('lastname', 'like', "%{$search}%");
                            })
                            ->orWhereHas('items.category', function ($category) use ($search) {
                                $category->where('name', 'like', "%{$search}%")
                                    ->orWhere('code', 'like', "%{$search}%");
                            });
                    });
                }
            )
            ->when(
                $request->status,
                function ($query, $status) {
                    if ($status === 'paid') {
                        $query->whereColumn('paid_amount', '=', 'total_amount');
                    } elseif ($status === 'partial') {
                        $query->where('paid_amount', '>', 0)
                            ->whereColumn('paid_amount', '<', 'total_amount');
                    } elseif ($status === 'pending') {
                        $query->where('paid_amount', '<=', 0);
                    } elseif ($status === 'advance') {
                        $query->whereColumn('paid_amount', '>', 'total_amount');
                    }
                }
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $summary = [
            'total_collection' => Fee::where('school_id', $schoolId)
                ->where('academic_year_id', $academicYear->id)
                ->sum('paid_amount'),
            'pending_collection' => Fee::where('school_id', $schoolId)
                ->where('academic_year_id', $academicYear->id)
                ->sum('balance'),
            'advance_collection' => Fee::where('school_id', $schoolId)
                ->where('academic_year_id', $academicYear->id)
                ->selectRaw('COALESCE(SUM(CASE WHEN paid_amount > total_amount THEN paid_amount - total_amount ELSE 0 END), 0) as total')
                ->value('total'),
            'total_invoices' => Fee::where('school_id', $schoolId)
                ->where('academic_year_id', $academicYear->id)
                ->count(),
        ];

        return view(
            'admin.finance.fee-management.payments',
            compact('fees', 'summary')
        );
    }

    /**
     * Show fee analytics and reports
     */
    public function analytics(Request $request)
    {
        $schoolId = Auth::user()->school_id;
        $academicYear = SiteHelper::getAcademicYear($schoolId);

        $monthlyCollection = Fee::selectRaw('MONTH(created_at) as month, SUM(paid_amount) as total')
            ->where('school_id', $schoolId)
            ->where('academic_year_id', $academicYear->id)
            ->whereRaw('balance = 0 OR paid_amount >= total_amount')
            ->groupByRaw('MONTH(created_at)')
            ->orderBy('month')
            ->get();

        $categoryBreakdown = FeeCategory::with(['structureItems' => function ($query) use ($schoolId, $academicYear) {
            $query->where('school_id', $schoolId);
        }])
        ->where('school_id', $schoolId)
        ->where('academic_year_id', $academicYear->id)
        ->get();

        // Build payment status breakdown manually
        $paidCount = Fee::where('school_id', $schoolId)
            ->where('academic_year_id', $academicYear->id)
            ->whereRaw('balance = 0 OR paid_amount >= total_amount')
            ->count();

        $paidAmount = Fee::where('school_id', $schoolId)
            ->where('academic_year_id', $academicYear->id)
            ->whereRaw('balance = 0 OR paid_amount >= total_amount')
            ->sum('total_amount');

        $pendingCount = Fee::where('school_id', $schoolId)
            ->where('academic_year_id', $academicYear->id)
            ->whereRaw('balance > 0')
            ->count();

        $pendingAmount = Fee::where('school_id', $schoolId)
            ->where('academic_year_id', $academicYear->id)
            ->whereRaw('balance > 0')
            ->sum('balance');

        $paymentStatusBreakdown = collect([
            (object) ['payment_status' => 'paid', 'count' => $paidCount, 'total' => $paidAmount ?? 0],
            (object) ['payment_status' => 'pending', 'count' => $pendingCount, 'total' => $pendingAmount ?? 0],
        ]);

        return view(
            'admin.finance.fee-management.analytics',
            compact('monthlyCollection', 'categoryBreakdown', 'paymentStatusBreakdown')
        );
    }

    /**
     * Export fees data
     */
    public function export(Request $request)
    {
        $schoolId = Auth::user()->school_id;
        $academicYear = SiteHelper::getAcademicYear($schoolId);

        $fees = Fee::with([
                'student.userprofile',
                'studentAcademic.standardLink.standard',
                'studentAcademic.standardLink.section',
                'items.category',
            ])
            ->where('school_id', $schoolId)
            ->where('academic_year_id', $academicYear->id)
            ->get();

        // CSV export logic
        $csv = "Invoice No,Student,Fee Categories,Total Amount,Paid Amount,Balance,Advance,Status,Date\n";
        foreach ($fees as $fee) {
            $profile = optional($fee->student)->userprofile;
            $student = trim(($profile->firstname ?? '') . ' ' . ($profile->lastname ?? ''));
            $categories = $fee->items->pluck('category.name')->filter()->implode(' | ');
            $csv .= implode(',', [
                $this->csvValue($fee->invoice_no),
                $this->csvValue($student),
                $this->csvValue($categories),
                $fee->total_amount,
                $fee->paid_amount,
                $fee->balance_amount,
                $fee->advance_amount,
                $this->csvValue($fee->erp_status),
                $fee->created_at->format('Y-m-d'),
            ]) . "\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="fee-report-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    private function csvValue(mixed $value): string
    {
        return '"' . str_replace('"', '""', (string) $value) . '"';
    }
}
