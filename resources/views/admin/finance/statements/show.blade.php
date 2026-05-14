@extends('layouts.admin.layout')

@section('title', 'Parent Account Statement')

@section('content')
<div class="relative max-w-5xl mx-auto mb-10">

    {{-- Action Bar --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Account Statement</h1>
            <p class="text-sm text-gray-500">
                {{ optional($student->userprofile)->firstname }} {{ optional($student->userprofile)->lastname }} 
                (ID: #{{ $student->id }})
            </p>
        </div>
        <div>
            <a href="{{ route('finance.statements.pdf', $student->id) }}" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center text-sm shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Print / Download PDF
            </a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded border p-4 shadow-sm">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Billed</p>
            <p class="text-xl font-bold text-gray-800 mt-1">Rs. {{ number_format($statement['summary']['total_billed'], 2) }}</p>
        </div>
        <div class="bg-white rounded border p-4 shadow-sm border-l-4 border-green-500">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Paid</p>
            <p class="text-xl font-bold text-green-700 mt-1">Rs. {{ number_format($statement['summary']['total_paid'], 2) }}</p>
        </div>
        <div class="bg-white rounded border p-4 shadow-sm border-l-4 border-red-500">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Pending</p>
            <p class="text-xl font-bold text-red-700 mt-1">Rs. {{ number_format($statement['summary']['total_pending'], 2) }}</p>
        </div>
        <div class="bg-white rounded border p-4 shadow-sm border-l-4 border-blue-500">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Advance Credit</p>
            <p class="text-xl font-bold text-blue-700 mt-1">Rs. {{ number_format($statement['summary']['advance_balance'], 2) }}</p>
        </div>
    </div>

    {{-- Statement Ledger --}}
    <div class="bg-white rounded border shadow-sm overflow-hidden">
        <div class="px-4 py-3 bg-gray-50 border-b">
            <h3 class="font-bold text-gray-700">Chronological Account Activity</h3>
        </div>
        
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Description</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Charge (Dr)</th>
                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Payment (Cr)</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                
                {{-- Invoices --}}
                @foreach($statement['invoices'] as $invoice)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $invoice->created_at->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                        Invoice Generated: {{ $invoice->invoice_no }} 
                        @if($invoice->payment_period)
                            ({{ \Carbon\Carbon::createFromFormat('Y-m', $invoice->payment_period)->format('M Y') }})
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 text-right font-medium">
                        Rs. {{ number_format($invoice->total_amount, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400 text-right">
                        -
                    </td>
                </tr>
                @endforeach

                {{-- Payments --}}
                @foreach($statement['payments'] as $payment)
                <tr class="hover:bg-gray-50 bg-green-50/30">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') : $payment->created_at->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                        Payment Received: {{ $payment->receipt_no }} ({{ ucfirst($payment->payment_method) }})
                        <div class="text-xs text-gray-500">Against Invoice: {{ optional($payment->fee)->invoice_no }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400 text-right">
                        -
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-700 text-right font-bold">
                        Rs. {{ number_format($payment->amount, 2) }}
                    </td>
                </tr>
                @endforeach
                
                {{-- Advance Adjustments --}}
                @foreach($statement['ledgers'] as $ledger)
                @if($ledger->transaction_type == \App\Models\StudentFeeLedger::TYPE_ADVANCE_APPLIED)
                <tr class="hover:bg-gray-50 bg-blue-50/30">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ \Carbon\Carbon::parse($ledger->created_at)->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                        Advance Applied
                        <div class="text-xs text-gray-500">{{ $ledger->remarks }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400 text-right">
                        -
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-700 text-right font-bold">
                        Rs. {{ number_format($ledger->amount, 2) }}
                    </td>
                </tr>
                @endif
                @endforeach

            </tbody>
        </table>
    </div>

</div>
@endsection
