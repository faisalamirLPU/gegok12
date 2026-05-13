@extends('layouts.admin.layout')

@section('title', 'Payment Receipt')

@section('content')
<div class="max-w-4xl mx-auto bg-white custom-shadow border p-10">
    <div class="flex flex-wrap lg:flex-row justify-between mb-10">
        <div>
            <h1 class="admin-h1">Payment Receipt</h1>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Receipt #: <span class="text-blue-700 font-mono">{{ $payment->receipt_no }}</span></p>
        </div>
        <div class="text-right">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Date: {{ date('d-M-Y', strtotime($payment->payment_date)) }}</p>
            <button onclick="window.print()" class="no-underline text-white px-6 inline-flex items-center custom-green py-1.5 justify-center mt-2">
                <span class="text-[10px] font-bold uppercase tracking-wider">Print Receipt</span>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-10">
        <div>
            <h2 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 border-b pb-1">School Details</h2>
            <p class="text-sm font-bold text-gray-800">{{ optional(Auth::user()->school)->name }}</p>
            <p class="text-xs text-gray-500 mt-1 leading-relaxed">{{ optional(Auth::user()->school)->address }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ optional(Auth::user()->school)->phone }}</p>
        </div>
        <div>
            <h2 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 border-b pb-1">Student Details</h2>
            <p class="text-sm font-bold text-gray-800">{{ optional($payment->student->userprofile)->firstname }} {{ optional($payment->student->userprofile)->lastname }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ optional($payment->student)->name }}</p>
            <p class="text-xs text-gray-600 font-bold mt-1">Invoice: <span class="font-mono text-blue-700">{{ optional($payment->fee)->invoice_no }}</span></p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10 bg-gray-50/50 border p-6">
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Receipt Amount</p>
            <p class="text-xl font-bold text-green-700">₹{{ number_format($payment->amount, 0) }}</p>
        </div>
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Payment Method</p>
            <p class="text-sm font-bold text-gray-800 uppercase">{{ ucfirst($payment->payment_method) }}</p>
        </div>
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Transaction ID</p>
            <p class="text-sm font-bold text-gray-800 font-mono">{{ $payment->transaction_id ?? 'N/A' }}</p>
        </div>
    </div>

    <div class="bg-gray-50 border p-6">
        <h2 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Receipt Notes</h2>
        <p class="text-xs text-gray-500 leading-relaxed italic">This receipt confirms receipt of the payment for the selected fee invoice. Keep this document for school records and verification.</p>
    </div>
</div>
@endsection
