<?php

namespace App\Services\Finance;

use App\Models\Fee;
use App\Models\Payment;
use App\Models\StudentFeeLedger;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinanceReportService
{
    /**
     * Get Daily Collection Summary
     */
    public function getDailyCollection(int $schoolId, string $date)
    {
        $dateStr = Carbon::parse($date)->toDateString();

        $payments = Payment::where('school_id', $schoolId)
            ->whereDate('payment_date', $dateStr)
            ->get();

        $advanceReceived = StudentFeeLedger::where('school_id', $schoolId)
            ->where('transaction_type', StudentFeeLedger::TYPE_ADVANCE_RECEIVED)
            ->whereDate('created_at', $dateStr)
            ->sum('amount');

        return [
            'total' => $payments->sum('amount') + $advanceReceived,
            'cash' => $payments->where('payment_method', 'cash')->sum('amount'),
            'online' => $payments->where('payment_method', 'online')->sum('amount'),
            'cheque' => $payments->where('payment_method', 'cheque')->sum('amount'),
            'advance_received' => $advanceReceived,
            'payments' => $payments, // Optional: if detailed list is needed
        ];
    }

    /**
     * Get Monthly Collection
     */
    public function getMonthlyCollection(int $schoolId, int $academicYearId)
    {
        $payments = Payment::select(
            DB::raw('MONTH(payment_date) as month'),
            DB::raw('YEAR(payment_date) as year'),
            DB::raw('SUM(amount) as total')
        )
            ->where('school_id', $schoolId)
            ->where('academic_year_id', $academicYearId)
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return $payments;
    }

    /**
     * Get Outstanding Dues Report
     */
    public function getOutstandingDues(int $schoolId, int $academicYearId, array $filters = [])
    {
        $query = Fee::with(['student.userprofile', 'studentAcademic.standardLink.standard', 'studentAcademic.standardLink.section'])
            ->where('school_id', $schoolId)
            ->where('academic_year_id', $academicYearId)
            ->whereIn('status', [Fee::STATUS_PENDING, Fee::STATUS_PARTIAL]);

        if (!empty($filters['class_id'])) {
            $query->whereHas('studentAcademic', function($q) use ($filters) {
                $q->whereHas('standardLink', function($sq) use ($filters) {
                    $sq->where('standard_id', $filters['class_id']);
                });
            });
        }

        if (!empty($filters['section_id'])) {
            $query->whereHas('studentAcademic', function($q) use ($filters) {
                $q->whereHas('standardLink', function($sq) use ($filters) {
                    $sq->where('section_id', $filters['section_id']);
                });
            });
        }

        if (!empty($filters['due_date_from'])) {
            $query->whereDate('due_date', '>=', Carbon::parse($filters['due_date_from']));
        }

        if (!empty($filters['due_date_to'])) {
            $query->whereDate('due_date', '<=', Carbon::parse($filters['due_date_to']));
        }

        $fees = $query->get();

        return [
            'total_outstanding' => $fees->sum('balance'),
            'overdue_count' => $fees->where('due_date', '<', now())->count(),
            'partial_count' => $fees->where('status', Fee::STATUS_PARTIAL)->count(),
            'unpaid_count' => $fees->where('status', Fee::STATUS_PENDING)->count(),
            'records' => $fees, // could be paginated
        ];
    }

    /**
     * Get Advance Balance Report
     */
    public function getAdvanceBalances(int $schoolId)
    {
        // Because advance balance is calculated iteratively, it's better to fetch via group by
        $ledgers = DB::table('student_fee_ledger')
            ->select('user_id', 
                DB::raw('SUM(CASE WHEN transaction_type = "' . StudentFeeLedger::TYPE_ADVANCE_RECEIVED . '" THEN amount ELSE 0 END) as total_received'),
                DB::raw('SUM(CASE WHEN transaction_type = "' . StudentFeeLedger::TYPE_ADVANCE_APPLIED . '" THEN amount ELSE 0 END) as total_applied')
            )
            ->where('school_id', $schoolId)
            ->groupBy('user_id')
            ->havingRaw('(total_received - total_applied) > 0')
            ->get();
            
        $userIds = $ledgers->pluck('user_id')->toArray();
        $users = User::with(['userprofile', 'studentAcademic.standardLink.standard', 'studentAcademic.standardLink.section'])
            ->whereIn('id', $userIds)
            ->get()
            ->keyBy('id');
            
        $records = [];
        foreach ($ledgers as $l) {
            $records[] = [
                'user' => $users[$l->user_id] ?? null,
                'balance' => $l->total_received - $l->total_applied,
                'total_received' => $l->total_received,
                'total_applied' => $l->total_applied,
            ];
        }

        return $records;
    }

    /**
     * Get Fee Aging Report
     */
    public function getAgingReport(int $schoolId, int $academicYearId)
    {
        $today = now();
        $thirtyDays = now()->subDays(30);
        $sixtyDays = now()->subDays(60);
        $ninetyDays = now()->subDays(90);

        $fees = Fee::with('student.userprofile')
            ->where('school_id', $schoolId)
            ->where('academic_year_id', $academicYearId)
            ->whereIn('status', [Fee::STATUS_PENDING, Fee::STATUS_PARTIAL])
            ->where('due_date', '<', $today)
            ->get();

        $aging = [
            '0_30' => ['count' => 0, 'amount' => 0, 'invoices' => []],
            '31_60' => ['count' => 0, 'amount' => 0, 'invoices' => []],
            '61_90' => ['count' => 0, 'amount' => 0, 'invoices' => []],
            '90_plus' => ['count' => 0, 'amount' => 0, 'invoices' => []],
        ];

        foreach ($fees as $fee) {
            $days = $today->diffInDays($fee->due_date);
            $amount = (float) $fee->balance;

            if ($days <= 30) {
                $bucket = '0_30';
            } elseif ($days <= 60) {
                $bucket = '31_60';
            } elseif ($days <= 90) {
                $bucket = '61_90';
            } else {
                $bucket = '90_plus';
            }

            $aging[$bucket]['count']++;
            $aging[$bucket]['amount'] += $amount;
            $aging[$bucket]['invoices'][] = $fee;
        }

        return $aging;
    }
}
