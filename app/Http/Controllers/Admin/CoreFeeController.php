<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CoreFeeHead;
use App\Models\CoreFeeInvoice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoreFeeController extends Controller
{
    public function index()
    {
        return view('admin.core.fees.index', [
            'heads' => CoreFeeHead::where('school_id', Auth::user()->school_id)->latest()->get(),
            'students' => User::where('school_id', Auth::user()->school_id)
                ->where('usergroup_id', User::STUDENT_USERGROUP_ID)
                ->where('status', 'active')
                ->orderBy('name')
                ->get(),
            'invoices' => CoreFeeInvoice::with(['feeHead', 'student.userprofile'])
                ->where('school_id', Auth::user()->school_id)
                ->latest()
                ->paginate(25),
        ]);
    }

    public function storeHead(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'fee_type' => 'required|in:monthly,one_time',
            'amount' => 'required|numeric|min:0',
            'due_day' => 'required|integer|min:1|max:28',
        ]);

        CoreFeeHead::create($data + ['school_id' => Auth::user()->school_id, 'is_active' => true]);

        return back()->with('successmessage', 'Fee head created successfully.');
    }

    public function generate(Request $request)
    {
        $data = $request->validate([
            'core_fee_head_id' => 'required|exists:core_fee_heads,id',
            'fee_month' => 'nullable|integer|min:1|max:12',
            'fee_year' => 'nullable|integer|min:2020|max:2100',
            'student_id' => 'nullable|exists:users,id',
        ]);

        $head = CoreFeeHead::where('school_id', Auth::user()->school_id)->findOrFail($data['core_fee_head_id']);
        $month = $head->fee_type === 'monthly' ? (int) ($data['fee_month'] ?? now()->month) : null;
        $year = $head->fee_type === 'monthly' ? (int) ($data['fee_year'] ?? now()->year) : null;
        $dueDate = $head->fee_type === 'monthly'
            ? Carbon::create($year, $month, min((int) $head->due_day, 28))->toDateString()
            : now()->addDays(15)->toDateString();

        $studentQuery = User::where('school_id', Auth::user()->school_id)
            ->where('usergroup_id', User::STUDENT_USERGROUP_ID)
            ->where('status', 'active');

        if (!empty($data['student_id'])) {
            $studentQuery->where('id', $data['student_id']);
        }

        $students = $studentQuery->get();
        if ($students->isEmpty()) {
            return back()->with('errormessage', 'No valid student found for fee generation.');
        }

        foreach ($students as $student) {
            CoreFeeInvoice::firstOrCreate(
                [
                    'core_fee_head_id' => $head->id,
                    'student_id' => $student->id,
                    'fee_month' => $month,
                    'fee_year' => $year,
                ],
                [
                    'school_id' => Auth::user()->school_id,
                    'amount' => $head->amount,
                    'due_date' => $dueDate,
                    'status' => 'unpaid',
                ]
            );
        }

        return back()->with('successmessage', 'Fee invoices generated successfully.');
    }

    public function markPaid(Request $request, CoreFeeInvoice $invoice)
    {
        abort_unless((int) $invoice->school_id === (int) Auth::user()->school_id, 404);

        $data = $request->validate([
            'payment_mode' => 'nullable|string|max:100',
            'transaction_reference' => 'nullable|string|max:255',
        ]);

        $invoice->update($data + [
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        return back()->with('successmessage', 'Payment marked as paid.');
    }

    public function receipt(CoreFeeInvoice $invoice)
    {
        abort_unless((int) $invoice->school_id === (int) Auth::user()->school_id, 404);

        return view('admin.core.fees.receipt', compact('invoice'));
    }
}
