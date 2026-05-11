<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Services\Finance\FeePaymentService;
use Illuminate\Http\Request;

class FeePaymentController extends Controller
{
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
            \App\Helpers\SiteHelper::getAcademicYear(
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
}
