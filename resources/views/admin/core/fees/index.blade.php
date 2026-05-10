@extends('layouts.admin.layout')

@section('content')
<div>
    <h1 class="admin-h1 font-plex my-3">Fee Management</h1>
    <p class="text-gray-500 mb-4">Create monthly or one-time fees, generate student invoices, and record payments.</p>
    @include('partials.message')

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 mb-5">
        <form method="POST" action="{{ url('/admin/fees/heads') }}" class="bg-white custom-shadow border p-5">
            @csrf
            <h2 class="text-xl font-semibold mb-3">Create Fee Head</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <input name="name" placeholder="Fee name" class="border px-3 py-2" required>
                <select name="fee_type" class="border px-3 py-2" required>
                    <option value="monthly">Monthly</option>
                    <option value="one_time">One Time</option>
                </select>
                <input type="number" step="0.01" name="amount" placeholder="Amount" class="border px-3 py-2" required>
                <input type="number" min="1" max="28" name="due_day" value="10" class="border px-3 py-2" required>
            </div>
            <button class="bg-red-700 text-white px-4 py-2 rounded mt-4">Save Fee</button>
        </form>

        <form method="POST" action="{{ url('/admin/fees/generate') }}" class="bg-white custom-shadow border p-5">
            @csrf
            <h2 class="text-xl font-semibold mb-3">Generate Invoices</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <select name="core_fee_head_id" class="border px-3 py-2" required>
                    <option value="">Select fee</option>
                    @foreach($heads as $head)
                        <option value="{{ $head->id }}">{{ $head->name }} ({{ $head->fee_type }})</option>
                    @endforeach
                </select>
                <select name="student_id" class="border px-3 py-2">
                    <option value="">All students</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}">{{ optional($student->userprofile)->firstname }} {{ optional($student->userprofile)->lastname }} ({{ $student->name }})</option>
                    @endforeach
                </select>
                <input type="number" min="1" max="12" name="fee_month" value="{{ now()->month }}" class="border px-3 py-2">
                <input type="number" name="fee_year" value="{{ now()->year }}" class="border px-3 py-2">
            </div>
            <p class="text-sm text-gray-500 mt-2">Select a student to generate an invoice for one student only. Leave blank to generate for all active students.</p>
            <button class="bg-red-700 text-white px-4 py-2 rounded mt-4">Generate For Students</button>
        </form>
    </div>

    <div class="bg-white custom-shadow border overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b bg-gray-50">
                    <th class="text-left py-3 px-4">Student</th>
                    <th class="text-left py-3 px-4">Fee</th>
                    <th class="text-left py-3 px-4">Period</th>
                    <th class="text-left py-3 px-4">Amount</th>
                    <th class="text-left py-3 px-4">Due</th>
                    <th class="text-left py-3 px-4">Status</th>
                    <th class="text-left py-3 px-4">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4">{{ optional($invoice->student->userprofile)->firstname }} {{ optional($invoice->student->userprofile)->lastname }}</td>
                        <td class="py-3 px-4">{{ $invoice->feeHead->name }}</td>
                        <td class="py-3 px-4">{{ $invoice->fee_month ? $invoice->fee_month.'/'.$invoice->fee_year : 'One time' }}</td>
                        <td class="py-3 px-4">{{ number_format((float) $invoice->amount, 2) }}</td>
                        <td class="py-3 px-4">{{ optional($invoice->due_date)->format('d-m-Y') ?? '-' }}</td>
                        <td class="py-3 px-4">{{ ucwords($invoice->status) }}</td>
                        <td class="py-3 px-4">
                            @if($invoice->status !== 'paid')
                                <form method="POST" action="{{ url('/admin/fees/invoices/'.$invoice->id.'/paid') }}" class="flex gap-2">
                                    @csrf
                                    <input name="payment_mode" placeholder="Mode" class="border px-2 py-1 w-20">
                                    <input name="transaction_reference" placeholder="Ref" class="border px-2 py-1 w-24">
                                    <button class="text-blue-600">Mark Paid</button>
                                </form>
                            @else
                                Paid
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="py-6 px-4 text-center text-gray-500">No invoices generated yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $invoices->links() }}</div>
</div>
@endsection
