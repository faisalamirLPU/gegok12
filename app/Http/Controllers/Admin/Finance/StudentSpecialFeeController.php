<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\FeeCategory;
use App\Models\StudentSpecialFee;
use App\Models\UserProfile;
use App\Helpers\SiteHelper;
use App\Services\Finance\FeeInvoiceService;

class StudentSpecialFeeController extends Controller
{
    public function __construct(
        protected FeeInvoiceService $feeInvoiceService
    ) {
    }

    public function index()
    {
        $fees = StudentSpecialFee::with([
            'student.userprofile',
            'feeCategory'
        ])
        ->latest()
        ->paginate(20);

        return view(
            'admin.finance.special-fees.index',
            compact('fees')
        );
    }

    public function create()
    {
        $students = User::where(
                'school_id',
                auth()->user()->school_id
            )
            ->where(
                'usergroup_id',
                User::STUDENT_USERGROUP_ID
            )
            ->with([
                'userprofile',
                'studentAcademic.standardLink.standard',
                'studentAcademic.standardLink.section'
            ])
            ->get();

        $categories = FeeCategory::where(
                'school_id',
                auth()->user()->school_id
            )
            ->where(
                'academic_year_id',
                \App\Helpers\SiteHelper::getAcademicYear(
                    auth()->user()->school_id
                )->id
            )
            ->where('status', 1)
            ->get();

        return view(
            'admin.finance.special-fees.create',
            compact(
                'students',
                'categories'
            )
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'         => 'required',
            'fee_category_id' => 'required',
            'amount'          => 'required|numeric|min:0',
            'due_date'        => 'nullable|date',
            'remarks'         => 'nullable'
        ]);

        $specialFee = StudentSpecialFee::create([
            'school_id'        => auth()->user()->school_id,
            'academic_year_id' => SiteHelper::getAcademicYear(
                auth()->user()->school_id
            )->id,

            'user_id'          => $request->user_id,
            'fee_category_id'  => $request->fee_category_id,
            'amount'           => $request->amount,
            'due_date'         => $request->due_date,
            'remarks'          => $request->remarks,
            'status'           => 1
        ]);

        $this->feeInvoiceService->appendSpecialFee($specialFee);

        return redirect()
            ->route('finance.special-fees.index')
            ->with(
                'success',
                'Special fee assigned successfully.'
            );
    }

    public function getFeeAmount(Request $request)
    {
        $category = FeeCategory::find(
            $request->fee_category_id
        );

        if (!$category) {
            return response()->json([
                'amount' => 0
            ]);
        }

        $feeStructureItem = \App\Models\FeeStructureItem::where(
                'fee_category_id',
                $category->id
            )
            ->first();

        return response()->json([
            'amount' => $feeStructureItem
                ? $feeStructureItem->amount
                : 0
        ]);
    }
}
