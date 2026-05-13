<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Helpers\SiteHelper;
use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Models\Payment;
use App\Services\Finance\FeePaymentService;
use Barryvdh\DomPDF\Facade\Pdf;
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
    | Download Receipt PDF
    |--------------------------------------------------------------------------
    */

    public function receipt($paymentId)
    {
        $payment = Payment::with([

            'fee.student.userprofile',

            'fee.studentAcademic.standardLink.standard',

            'fee.studentAcademic.standardLink.section',

        ])->findOrFail($paymentId);

        $school = auth()->user()->school;

        $pdf = Pdf::loadView(

            'admin.finance.receipts.pdf',

            compact('payment', 'school')
        );

        return $pdf->stream(
            'Receipt-' . $payment->receipt_no . '.pdf'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Invoice List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $academicYear = SiteHelper::getAcademicYear(
            auth()->user()->school_id
        );

        $fees = Fee::query()

            ->with([

                'student.userprofile',

                'studentAcademic.standardLink.standard',

                'studentAcademic.standardLink.section',

                'payments',
            ])

            ->where(
                'school_id',
                auth()->user()->school_id
            )

            ->where(
                'academic_year_id',
                $academicYear->id
            )

            ->when(
                $request->search,
                function ($query, $search) {

                    $query->where(function ($q) use ($search) {

                        $q->where(
                            'invoice_no',
                            'like',
                            "%{$search}%"
                        )

                            ->orWhereHas(
                                'student.userprofile',
                                function ($student) use ($search) {

                                    $student->where(
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
                    });
                }
            )

            ->latest()

            ->paginate(20)

            ->withQueryString();

        return view(
            'admin.finance.payments.index',
            compact('fees')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Create Payment
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
    | Store Payment (Modal Support)
    |--------------------------------------------------------------------------
    */

    public function storePayment(Request $request)
    {
        $validated = $request->validate([

            'fee_id' => 'required|exists:fees,id',

            'amount' => 'required|numeric|min:1',

            'payment_method' => 'required|string',

            'transaction_id' => 'nullable|string',

            'remarks' => 'nullable|string',

            'payment_date' => 'nullable|date',
        ]);

        $fee = Fee::findOrFail(
            $validated['fee_id']
        );

        $payment =
            $this->feePaymentService
                ->collectPayment(
                    $fee,
                    $validated
                );

        return redirect()

            ->route(
                'finance.payments.create',
                $fee->id
            )

            ->with([

                'success' =>
                    "Payment of Rs. {$payment->amount} recorded successfully.",

                'receipt_id' =>
                    $payment->id,
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Standard Store
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

            'payment_date' => 'nullable|date',
        ]);

        $validated['fee_id'] = $fee->id;

        $payment =
            $this->feePaymentService
                ->collectPayment(
                    $fee,
                    $validated
                );

        return redirect()

            ->route(
                'finance.payments.create',
                $fee->id
            )

            ->with([

                'success' =>
                    "Payment of Rs. {$payment->amount} recorded successfully.",

                'receipt_id' =>
                    $payment->id,
            ]);
    }
}
