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
    public function specialFees(Request $request)
    {
        $query = StudentSpecialFee::with([
            'student.userprofile',
            'feeCategory'
        ])
        ->where('school_id', Auth::user()->school_id)
        ->where('academic_year_id', SiteHelper::getAcademicYear(Auth::user()->school_id)->id);

        if ($request->status === 'archived') {
            $query->onlyTrashed();
        } elseif ($request->status === 'cancelled') {
            $query->where('status', StudentSpecialFee::STATUS_CANCELLED);
        } elseif ($request->status === 'applied') {
            $query->where('status', StudentSpecialFee::STATUS_APPLIED);
        } elseif ($request->status === 'active') {
            $query->where('status', StudentSpecialFee::STATUS_ACTIVE);
        }

        $specialFees = $query->latest()->paginate(20)->withQueryString();

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
            // ERP Filter: Only show valid invoices with items and correct format
            ->where('total_amount', '>', 0)
            ->where('invoice_no', 'like', 'INV-20%')
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

        $invoiceQuery = Fee::where('school_id', $schoolId)
            ->where('academic_year_id', $academicYear->id);

        $totalDemand = (clone $invoiceQuery)->sum('total_amount');
        $totalCollected = (clone $invoiceQuery)->sum('paid_amount');
        $totalPending = (clone $invoiceQuery)->sum('balance');
        $totalAdvance = (clone $invoiceQuery)
            ->selectRaw('COALESCE(SUM(CASE WHEN paid_amount > total_amount THEN paid_amount - total_amount ELSE 0 END), 0) as total')
            ->value('total');

        $collectionRate = $totalDemand > 0
            ? round($totalCollected / $totalDemand * 100, 1)
            : 0;

        $monthlyCollection = (clone $invoiceQuery)
            ->selectRaw('MONTH(created_at) as month, SUM(paid_amount) as total')
            ->groupByRaw('MONTH(created_at)')
            ->orderBy('month')
            ->get();

        $paidCount = (clone $invoiceQuery)
            ->whereRaw('balance <= 0')
            ->whereColumn('paid_amount', '<=', 'total_amount')
            ->count();

        $partialCount = (clone $invoiceQuery)
            ->where('paid_amount', '>', 0)
            ->where('balance', '>', 0)
            ->count();

        $pendingCount = (clone $invoiceQuery)
            ->where('paid_amount', '<=', 0)
            ->where('balance', '>', 0)
            ->count();

        $advanceCount = (clone $invoiceQuery)
            ->whereColumn('paid_amount', '>', 'total_amount')
            ->count();

        $categoryTotals = StudentSpecialFee::where('school_id', $schoolId)
            ->where('academic_year_id', $academicYear->id)
            ->where('status', StudentSpecialFee::STATUS_ACTIVE)
            ->groupBy('fee_category_id')
            ->selectRaw('fee_category_id, COALESCE(SUM(amount), 0) as total_special_amount')
            ->pluck('total_special_amount', 'fee_category_id');

        $categoryBreakdown = FeeCategory::with(['structureItems' => function ($query) use ($schoolId, $academicYear) {
                $query->where('school_id', $schoolId)
                    ->where('academic_year_id', $academicYear->id);
            }, 'feeItems'])
            ->where('school_id', $schoolId)
            ->where('academic_year_id', $academicYear->id)
            ->where('status', 1)
            ->get()
            ->map(function ($category) use ($categoryTotals) {
                $structureAmount = $category->structureItems->sum('amount');
                $specialAmount = $categoryTotals->get($category->id, 0);
                $category->analytics_total_amount = $structureAmount + $specialAmount;
                $category->analytics_structure_amount = $structureAmount;
                $category->analytics_special_amount = $specialAmount;
                return $category;
            });

        $paymentStatusBreakdown = collect([
            (object) ['payment_status' => 'paid', 'count' => $paidCount, 'total' => $totalCollected],
            (object) ['payment_status' => 'partial', 'count' => $partialCount, 'total' => (clone $invoiceQuery)->where('paid_amount', '>', 0)->where('balance', '>', 0)->sum('paid_amount')],
            (object) ['payment_status' => 'pending', 'count' => $pendingCount, 'total' => $totalPending],
            (object) ['payment_status' => 'advance', 'count' => $advanceCount, 'total' => $totalAdvance],
        ]);

        return view(
            'admin.finance.fee-management.analytics',
            compact(
                'monthlyCollection',
                'categoryBreakdown',
                'paymentStatusBreakdown',
                'totalDemand',
                'totalCollected',
                'totalPending',
                'totalAdvance',
                'collectionRate'
            )
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
