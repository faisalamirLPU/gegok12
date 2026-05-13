<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Models\School;
use Barryvdh\DomPDF\Facade\Pdf;

class FeeSlipController extends Controller
{
    public function show(Fee $fee)
    {
        $fee->load([
            'items.category',
            'student.userprofile',
            'studentAcademic.standardLink.standard',
            'studentAcademic.standardLink.section',
            'payments',
        ]);

        $school = School::findOrFail(
            auth()->user()->school_id
        );

        $pdf = Pdf::loadView(
            'admin.finance.receipts.fee-slip',
            compact('fee', 'school')
        );

        return $pdf->stream(
            'fee-slip-' . $fee->invoice_no . '.pdf'
        );
    }
}
