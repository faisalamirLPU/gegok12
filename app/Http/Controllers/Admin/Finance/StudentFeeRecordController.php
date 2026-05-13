<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Helpers\SiteHelper;
use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Models\StandardLink;
use App\Models\User;
use App\Services\Finance\AdvanceCreditService;
use App\Services\Finance\FeePaymentService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StudentFeeRecordController extends Controller
{
    protected AdvanceCreditService $advanceCreditService;

    protected FeePaymentService $feePaymentService;

    public function __construct(
        AdvanceCreditService $advanceCreditService,
        FeePaymentService $feePaymentService
    ) {
        $this->advanceCreditService = $advanceCreditService;

        $this->feePaymentService = $feePaymentService;
    }

    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        $academicYear = SiteHelper::getAcademicYear(
            $schoolId
        );

        $classes = StandardLink::query()

            ->with([
                'standard',
                'section',
                'studentAcademic.user.userprofile'
            ])

            ->where('school_id', $schoolId)

            ->where(
                'academic_year_id',
                $academicYear->id
            )

            ->get()

            ->map(function ($link) {

                $link->student_count =
                    $link->studentAcademic->count();

                return $link;
            })

            ->filter(
                fn($link) => $link->student_count > 0
            );

        return view(
            'admin.finance.fee-records.index',
            compact(
                'classes',
                'academicYear'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Class Records
    |--------------------------------------------------------------------------
    */

    public function byClass(
        Request $request,
        int $classId
    ) {
        $schoolId = auth()->user()->school_id;

        $academicYear = SiteHelper::getAcademicYear(
            $schoolId
        );

        $month = $request->get(
            'month',
            now()->month
        );

        $year = $request->get(
            'year',
            now()->year
        );

        $paymentPeriod =
            "{$year}-" .
            str_pad($month, 2, '0', STR_PAD_LEFT);

        $classLink = StandardLink::with([
                'standard',
                'section'
            ])

            ->where('school_id', $schoolId)

            ->findOrFail($classId);

        $students = User::where(
                'school_id',
                $schoolId
            )

            ->where(
                'usergroup_id',
                User::STUDENT_USERGROUP_ID
            )

            ->whereHas(
                'studentAcademic',
                function ($q) use (
                    $classId,
                    $academicYear
                ) {

                    $q->where(
                        'standardLink_id',
                        $classId
                    )

                    ->where(
                        'academic_year_id',
                        $academicYear->id
                    );
                }
            )

            ->with([
                'userprofile',

                'studentAcademic' =>
                    function ($q) use (
                        $classId,
                        $academicYear
                    ) {

                        $q->where(
                            'standardLink_id',
                            $classId
                        )

                        ->where(
                            'academic_year_id',
                            $academicYear->id
                        );
                    }
            ])

            ->get();

        $studentRecords = [];

        foreach ($students as $student) {

            $fee = Fee::where(
                    'school_id',
                    $schoolId
                )

                ->where(
                    'academic_year_id',
                    $academicYear->id
                )

                ->where(
                    'user_id',
                    $student->id
                )

                ->where(
                    'payment_period',
                    $paymentPeriod
                )

                ->with([
                    'items.category',
                    'payments'
                ])

                ->first();

            $advanceBalance =
                $this->advanceCreditService
                    ->getAdvanceBalance(
                        $schoolId,
                        $student->id
                    );

            $studentRecords[] = [

                'student' => $student,

                'fee' => $fee,

                'advance_balance' =>
                    $advanceBalance,
            ];
        }

        $summary = [

            'total_students' =>
                count($studentRecords),

            'paid_count' =>
                collect($studentRecords)

                    ->where(
                        'fee.erp_status',
                        'Paid'
                    )

                    ->count(),

            'partial_count' =>
                collect($studentRecords)

                    ->where(
                        'fee.erp_status',
                        'Partial'
                    )

                    ->count(),

            'pending_count' =>
                collect($studentRecords)

                    ->where(
                        'fee.erp_status',
                        'Pending'
                    )

                    ->count(),

            'no_invoice_count' =>
                collect($studentRecords)

                    ->filter(
                        fn($r) => !$r['fee']
                    )

                    ->count(),

            'total_demand' =>
                collect($studentRecords)

                    ->sum(
                        fn($r) =>
                            $r['fee']->total_amount ?? 0
                    ),

            'total_collected' =>
                collect($studentRecords)

                    ->sum(
                        fn($r) =>
                            $r['fee']->paid_amount ?? 0
                    ),

            'total_pending' =>
                collect($studentRecords)

                    ->sum(
                        fn($r) =>
                            $r['fee']->balance ?? 0
                    ),
        ];

        return view(
            'admin.finance.fee-records.by-class',
            compact(
                'classLink',
                'studentRecords',
                'summary',
                'paymentPeriod',
                'month',
                'year',
                'academicYear'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Student Fee Record
    |--------------------------------------------------------------------------
    */

    public function show(
        Request $request,
        int $userId
    ) {
        $schoolId = auth()->user()->school_id;

        $academicYear = SiteHelper::getAcademicYear(
            $schoolId
        );

        $student = User::with([

                'userprofile',

                'studentAcademic.standardLink.standard',

                'studentAcademic.standardLink.section'

            ])

            ->where('school_id', $schoolId)

            ->findOrFail($userId);

        $fees = Fee::where(
                'school_id',
                $schoolId
            )

            ->where(
                'academic_year_id',
                $academicYear->id
            )

            ->where(
                'user_id',
                $userId
            )

            ->with([
                'items.category',
                'payments'
            ])

            ->orderBy(
                'payment_period',
                'desc'
            )

            ->get()

            ->map(function ($fee) {

                $fee->payment_history =
                    $fee->payments->map(
                        function ($payment) {

                            return [

                                'date' =>
                                    $payment->payment_date,

                                'receipt_no' =>
                                    $payment->receipt_no,

                                'amount' =>
                                    $payment->amount,

                                'method' =>
                                    $payment->payment_method,

                                'remarks' =>
                                    $payment->remarks,
                            ];
                        }
                    );

                return $fee;
            });

        /*
        |--------------------------------------------------------------------------
        | Safe Monthly Grouping
        |--------------------------------------------------------------------------
        */

        $monthlyFees = $fees

            ->groupBy(function ($fee) {

                return $fee->payment_period ?: 'unknown';
            })

            ->map(function ($group, $period) {

                /*
                |--------------------------------------------------------------------------
                | Safe Month Formatting
                |--------------------------------------------------------------------------
                */

                $monthName = 'Unknown Period';

                if (
                    !empty($period) &&
                    $period !== 'unknown'
                ) {

                    try {

                        $monthName =
                            Carbon::createFromFormat(
                                'Y-m',
                                $period
                            )->format('F Y');

                    } catch (\Exception $e) {

                        $monthName =
                            'Invalid Period';
                    }
                }

                return [

                    'period' => $period,

                    'month_name' => $monthName,

                    'fee' => $group->first(),

                    'items' =>
                        $group->first()->items
                        ?? collect(),
                ];
            })

            ->sortByDesc('period')

            ->values();

        /*
        |--------------------------------------------------------------------------
        | Advance Balance
        |--------------------------------------------------------------------------
        */

        $advanceBalance =
            $this->advanceCreditService
                ->getAdvanceBalance(
                    $schoolId,
                    $userId
                );

        $advanceLedger =
            $this->advanceCreditService
                ->getLedgerEntries(
                    $userId
                );

        /*
        |--------------------------------------------------------------------------
        | Lifetime Summary
        |--------------------------------------------------------------------------
        */

        $lifetimeSummary = [

            'total_demand' =>
                $fees->sum('total_amount'),

            'total_paid' =>
                $fees->sum('paid_amount'),

            'total_pending' =>
                $fees->sum('balance_amount'),

            'advance_balance' =>
                $advanceBalance,

            'total_invoices' =>
                $fees->count(),

            'paid_invoices' =>
                $fees
                    ->where(
                        'erp_status',
                        'Paid'
                    )
                    ->count(),
        ];

        return view(
            'admin.finance.fee-records.show',
            compact(
                'student',
                'monthlyFees',
                'lifetimeSummary',
                'advanceBalance',
                'advanceLedger',
                'academicYear'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Payment Periods
    |--------------------------------------------------------------------------
    */

    public function getPaymentPeriods(
        int $userId
    ) {
        $schoolId = auth()->user()->school_id;

        $academicYear = SiteHelper::getAcademicYear(
            $schoolId
        );

        $periods = Fee::where(
                'school_id',
                $schoolId
            )

            ->where(
                'academic_year_id',
                $academicYear->id
            )

            ->where(
                'user_id',
                $userId
            )

            ->select('payment_period')

            ->distinct()

            ->orderBy(
                'payment_period',
                'desc'
            )

            ->pluck('payment_period');

        return response()->json([

            'periods' => $periods

                ->filter()

                ->map(function ($p) {

                    try {

                        return [

                            'value' => $p,

                            'label' =>
                                Carbon::createFromFormat(
                                    'Y-m',
                                    $p
                                )->format('F Y'),
                        ];

                    } catch (\Exception $e) {

                        return [

                            'value' => $p,

                            'label' =>
                                'Invalid Period',
                        ];
                    }
                })
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Apply Advance
    |--------------------------------------------------------------------------
    */

    public function applyAdvance(
        Request $request,
        int $feeId
    ) {
        $request->validate([

            'amount' =>
                'required|numeric|min:0',
        ]);

        $fee = Fee::findOrFail($feeId);

        $result = $this->feePaymentService

            ->applyAdvancePayment(
                $fee,
                (float) $request->amount,
                $request->remarks
            );

        if ($result['success']) {

            return redirect()

                ->back()

                ->with(
                    'success',
                    $result['message']
                );
        }

        return redirect()

            ->back()

            ->with(
                'error',
                $result['message']
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Toggle Lock
    |--------------------------------------------------------------------------
    */

    public function toggleLock(
        Request $request,
        int $feeId
    ) {
        $fee = Fee::findOrFail($feeId);

        if ($fee->is_locked) {

            $fee->unlock();

            $message =
                'Invoice unlocked successfully.';

        } else {

            $fee->lock();

            $message =
                'Invoice locked successfully.';
        }

        if ($request->ajax()) {

            return response()->json([

                'success' => true,

                'message' => $message
            ]);
        }

        return redirect()

            ->back()

            ->with(
                'success',
                $message
            );
    }
}