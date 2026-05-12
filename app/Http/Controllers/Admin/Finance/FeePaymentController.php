<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Helpers\SiteHelper;
use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Services\Finance\FeePaymentService;
use Illuminate\Http\Request;

class FeePaymentController extends Controller
{
    protected FeePaymentService $feePaymentService;

    public function __construct(
        FeePaymentService $feePaymentService
    ) {
        $this->feePaymentService = $feePaymentService;
    }

    /*
    |--------------------------------------------------------------------------
    | Invoice List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $fees = Fee::query()

            ->with([
                'student.userprofile',
                'studentAcademic.standardLink.standard',
                'studentAcademic.standardLink.section'
            ])

            ->where(
                'school_id',
                auth()->user()->school_id
            )

            ->where(
                'academic_year_id',
                SiteHelper::getAcademicYear(
                    auth()->user()->school_id
                )->id
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

            ->paginate(20);

        return view(
            'admin.finance.payments.index',
            compact('fees')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Create Payment Page
    |--------------------------------------------------------------------------
    */

    public function create(Fee $fee)
    {
        return view(
            'admin.finance.payments.create',
            compact('fee')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Store Payment
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request,
        Fee $fee
    ) {

        $validated = $request->validate([

            'amount' => 'required|numeric|min:1',

            'payment_method' => 'required|string',

            'transaction_id' => 'nullable|string',

            'remarks' => 'nullable|string',
        ]);

        $this->feePaymentService
            ->collectPayment($fee, $validated);

        return redirect()

            ->route('finance.payments.index')

            ->with(
                'success',
                'Payment collected successfully.'
            );
    }
}
