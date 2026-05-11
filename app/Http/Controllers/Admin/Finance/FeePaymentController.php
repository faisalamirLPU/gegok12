<?php

namespace App\Http\Controllers\Admin\Finance;

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

    public function index()
    {
        $fees = Fee::query()

            ->latest()

            ->paginate(20);

        return view(
            'admin.finance.payments.index',
            compact('fees')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Payment Form
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
