<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\FeeCategory;
use App\Models\StudentSpecialFee;
use App\Models\UserProfile;
use App\Helpers\SiteHelper;
use App\Services\Finance\MonthlyInvoiceGeneratorService;
use App\Services\Finance\StudentSpecialFeeService;

class StudentSpecialFeeController extends Controller
{
    public function __construct(
        protected \App\Services\Finance\MonthlyInvoiceGeneratorService $invoiceGenerator,
        protected StudentSpecialFeeService $specialFeeService
    ) {
    }

    public function index(Request $request)
    {
        $fees = StudentSpecialFee::with([
            'student.userprofile',
            'feeCategory'
        ]);

        if ($request->status === 'archived') {
            $fees->onlyTrashed();
        } elseif ($request->status === 'cancelled') {
            $fees->where('status', StudentSpecialFee::STATUS_CANCELLED);
        }

        $fees = $fees->latest()->paginate(20)->withQueryString();

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

        // ERP Finance Automation: Generate Or Update invoice to include special fee
        $this->invoiceGenerator->generateMonthlyInvoice(
            (int) $specialFee->school_id,
            (int) $specialFee->academic_year_id,
            (int) $specialFee->user_id,
            $specialFee->due_date ? \Carbon\Carbon::parse($specialFee->due_date) : now()
        );

        return redirect()
            ->route('finance.fee-management.special-fees')
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

    /**
     * Show the form for editing the specified special fee.
     */
    public function edit(StudentSpecialFee $specialFee)
    {
        $students = User::where('school_id', auth()->user()->school_id)
            ->where('usergroup_id', User::STUDENT_USERGROUP_ID)
            ->with(['userprofile', 'studentAcademic.standardLink.standard', 'studentAcademic.standardLink.section'])
            ->get();

        $categories = FeeCategory::where('school_id', auth()->user()->school_id)
            ->where('academic_year_id', SiteHelper::getAcademicYear(auth()->user()->school_id)->id)
            ->where('status', 1)
            ->get();

        return view('admin.finance.special-fees.create', compact('specialFee', 'students', 'categories'));
    }

    /**
     * Update the specified special fee in storage.
     */
    public function update(Request $request, StudentSpecialFee $specialFee)
    {
        $request->validate([
            'user_id'         => 'required',
            'fee_category_id' => 'required',
            'amount'          => 'required|numeric|min:0',
            'due_date'        => 'nullable|date',
            'remarks'         => 'nullable'
        ]);

        $specialFee->update([
            'user_id'          => $request->user_id,
            'fee_category_id'  => $request->fee_category_id,
            'amount'           => $request->amount,
            'due_date'         => $request->due_date,
            'remarks'          => $request->remarks,
        ]);

        return redirect()
            ->route('finance.fee-management.special-fees')
            ->with('success', 'Special fee assignment updated successfully.');
    }

    /**
     * Remove the specified special fee from storage.
     */
    public function destroy(StudentSpecialFee $specialFee)
    {
        $result = $this->specialFeeService->delete($specialFee);

        return redirect()
            ->route('finance.fee-management.special-fees')
            ->with('success', $result['message']);
    }

    public function restore($id)
    {
        $specialFee = StudentSpecialFee::withTrashed()->findOrFail($id);
        $this->specialFeeService->restore($specialFee);

        return redirect()
            ->route('finance.fee-management.special-fees')
            ->with('success', 'Special fee restored successfully.');
    }
}
