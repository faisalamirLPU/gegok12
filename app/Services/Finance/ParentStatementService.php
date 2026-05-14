<?php

namespace App\Services\Finance;

use App\Models\Fee;
use App\Models\Payment;
use App\Models\StudentFeeLedger;
use App\Models\User;

class ParentStatementService
{
    /**
     * Get full student account statement
     */
    public function getStudentStatement(int $schoolId, int $academicYearId, int $userId)
    {
        $student = User::with(['userprofile', 'studentAcademic.standardLink.standard', 'studentAcademic.standardLink.section'])
            ->where('id', $userId)
            ->where('school_id', $schoolId)
            ->firstOrFail();

        $invoices = Fee::with('items')
            ->where('school_id', $schoolId)
            ->where('academic_year_id', $academicYearId)
            ->where('user_id', $userId)
            ->orderBy('created_at', 'asc')
            ->get();

        $payments = Payment::with('fee')
            ->where('school_id', $schoolId)
            ->where('academic_year_id', $academicYearId)
            ->where('user_id', $userId)
            ->orderBy('created_at', 'asc')
            ->get();

        $ledgers = StudentFeeLedger::where('school_id', $schoolId)
            ->where('academic_year_id', $academicYearId)
            ->where('user_id', $userId)
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $totalBilled = $invoices->sum('total_amount');
        $totalPaid = $payments->sum('amount');
        
        $advanceBalance = $ledgers->where('transaction_type', StudentFeeLedger::TYPE_ADVANCE_RECEIVED)->sum('amount') 
                        - $ledgers->where('transaction_type', StudentFeeLedger::TYPE_ADVANCE_APPLIED)->sum('amount');

        $totalPending = $invoices->sum('balance');

        return [
            'student' => $student,
            'invoices' => $invoices,
            'payments' => $payments,
            'ledgers' => $ledgers,
            'summary' => [
                'total_billed' => $totalBilled,
                'total_paid' => $totalPaid,
                'total_pending' => $totalPending,
                'advance_balance' => $advanceBalance,
            ]
        ];
    }
}
