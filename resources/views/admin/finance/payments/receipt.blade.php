@extends('layouts.admin.layout')

@section('title', 'Payment Receipt')

@section('content')
<div class="max-w-4xl mx-auto bg-white shadow border p-8">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-semibold">Payment Receipt</h1>
            <p class="text-sm text-gray-600">Receipt #: {{ $payment->receipt_no }}</p>
        </div>
        <div class="text-left md:text-right">
            <p class="text-sm text-gray-600">Date: {{ date('d-m-Y', strtotime($payment->payment_date)) }}</p>
            <button onclick="window.print()" class="bg-blue-700 text-white px-4 py-2 rounded mt-2">Print Receipt</button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div>
            <h2 class="font-semibold mb-2">School Details</h2>
            <p class="font-medium">{{ optional(Auth::user()->school)->name }}</p>
            <p class="text-sm text-gray-600">{{ optional(Auth::user()->school)->address }}</p>
            <p class="text-sm text-gray-600">{{ optional(Auth::user()->school)->phone }}</p>
        </div>
        <div>
            <h2 class="font-semibold mb-2">Student Details</h2>
            <p class="font-medium">{{ optional($payment->student->userprofile)->firstname }} {{ optional($payment->student->userprofile)->lastname }}</p>
            <p class="text-sm text-gray-600">{{ optional($payment->student)->name }}</p>
            <p class="text-sm text-gray-600">Invoice: {{ optional($payment->fee)->invoice_no }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div>
            <p class="font-semibold">Receipt Amount</p>
            <p>₹{{ number_format($payment->amount, 2) }}</p>
        </div>
        <div>
            <p class="font-semibold">Payment Method</p>
            <p>{{ ucfirst($payment->payment_method) }}</p>
        </div>
        <div>
            <p class="font-semibold">Transaction ID</p>
            <p>{{ $payment->transaction_id ?? 'N/A' }}</p>
        </div>
    </div>

    <div class="bg-gray-50 rounded-lg p-4">
        <h2 class="font-semibold mb-3">Receipt Notes</h2>
        <p class="text-sm text-gray-700">This receipt confirms receipt of the payment for the selected fee invoice. Keep this document for school records and verification.</p>
    </div>
</div>
@endsection
