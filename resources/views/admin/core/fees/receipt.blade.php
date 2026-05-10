@extends('layouts.admin.layout')

@section('content')
<div class="max-w-4xl mx-auto bg-white shadow border p-8">
    <div class="flex items-start justify-between mb-8">
        <div>
            <h1 class="text-2xl font-semibold">Fee Receipt</h1>
            <p class="text-sm text-gray-600">Invoice #{{ $invoice->id }}</p>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-600">{{ date('d-m-Y') }}</p>
            <button onclick="window.print()" class="bg-red-700 text-white px-4 py-2 rounded mt-2">Print</button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div>
            <h2 class="font-semibold mb-2">School</h2>
            <p>{{ optional(Auth::user()->school)->name }}</p>
        </div>
        <div>
            <h2 class="font-semibold mb-2">Student</h2>
            <p>{{ optional($invoice->student->userprofile)->firstname }} {{ optional($invoice->student->userprofile)->lastname }}</p>
            <p class="text-sm text-gray-600">{{ optional($invoice->student)->name }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div>
            <p class="font-semibold">Fee Name</p>
            <p>{{ optional($invoice->feeHead)->name }}</p>
        </div>
        <div>
            <p class="font-semibold">Period</p>
            <p>{{ $invoice->fee_month ? $invoice->fee_month.'/'.$invoice->fee_year : 'One time' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div>
            <p class="font-semibold">Amount</p>
            <p>{{ number_format((float) $invoice->amount, 2) }}</p>
        </div>
        <div>
            <p class="font-semibold">Due Date</p>
            <p>{{ optional($invoice->due_date)->format('d-m-Y') ?? '-' }}</p>
        </div>
        <div>
            <p class="font-semibold">Paid On</p>
            <p>{{ optional($invoice->paid_at)->format('d-m-Y') ?? '-' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <p class="font-semibold">Payment Mode</p>
            <p>{{ $invoice->payment_mode ?? '-' }}</p>
        </div>
        <div>
            <p class="font-semibold">Transaction Reference</p>
            <p>{{ $invoice->transaction_reference ?? '-' }}</p>
        </div>
    </div>
</div>
@endsection
