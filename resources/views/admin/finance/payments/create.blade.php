@extends('layouts.admin.layout')

@section('title', 'Collect Payment')

@section('content')

<div class="relative">
    {{-- Header --}}
    <div class="flex flex-wrap lg:flex-row justify-between my-3">
        <div>
            <h1 class="admin-h1 my-3">Collect Payment</h1>
        </div>
        <div class="flex items-center">
            <a href="{{ route('finance.payments.index') }}" class="no-underline text-gray-600 px-4 flex items-center bg-gray-100 border rounded py-1.5 justify-center hover:bg-gray-200 transition">
                <span class="text-[10px] font-bold uppercase tracking-wider">Back to Payments</span>
            </a>
        </div>
    </div>

    @include('admin.finance.partials.tabs')

    {{-- Form --}}
    <form method="POST" action="{{ route('finance.payments.store', $fee->id) }}"
          class="bg-white custom-shadow border overflow-hidden">
        @csrf

        {{-- Invoice Information --}}
        <div class="border-b p-5 bg-gray-50/50">
            <h2 class="text-sm font-semibold text-gray-800 uppercase tracking-wider mb-4">Invoice Summary</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Invoice Number --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Invoice Number</label>
                    <div class="w-full border rounded px-4 py-2 bg-white text-sm font-semibold text-blue-700 font-mono">
                        {{ $fee->invoice_no }}
                    </div>
                </div>

                {{-- Balance --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Outstanding Balance</label>
                    <div class="w-full border border-red-100 rounded px-4 py-2 bg-red-50 text-red-700 text-sm font-bold">
                        Rs. {{ number_format($fee->balance, 0) }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Payment Information --}}
        <div class="p-5">
            <h2 class="text-sm font-semibold text-gray-800 uppercase tracking-wider mb-4">Transaction Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Amount --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Collection Amount <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="amount" required
                           placeholder="Enter amount to collect"
                           class="w-full border border-gray-300 rounded px-4 py-2 text-sm focus:border-blue-500 focus:ring-0">
                </div>

                {{-- Payment Method --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Payment Method <span class="text-red-500">*</span></label>
                    <select name="payment_method"
                            class="w-full border border-gray-300 rounded px-4 py-2 text-sm focus:border-blue-500 focus:ring-0">
                        <option value="cash">Cash Payment</option>
                        <option value="upi">UPI / Online</option>
                        <option value="bank">Bank Transfer</option>
                        <option value="cheque">Cheque</option>
                    </select>
                </div>

                {{-- Transaction ID --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Transaction Reference</label>
                    <input type="text" name="transaction_id"
                           placeholder="Enter transaction ID or cheque number"
                           class="w-full border border-gray-300 rounded px-4 py-2 text-sm focus:border-blue-500 focus:ring-0">
                </div>

                {{-- Remarks --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Administrative Remarks</label>
                    <textarea name="remarks" rows="2"
                              placeholder="Any additional notes about this payment..."
                              class="w-full border border-gray-300 rounded px-4 py-2 text-sm focus:border-blue-500 focus:ring-0"></textarea>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="bg-gray-50 border-t p-5 flex items-center gap-3">
            <button type="submit" class="no-underline text-white px-8 flex items-center custom-green py-2 justify-center shadow-sm hover:shadow-md transition">
                <span class="text-sm font-semibold">Post Payment</span>
            </button>
            <a href="{{ url()->previous() }}" class="no-underline text-gray-700 px-6 flex items-center bg-gray-100 border py-2 justify-center rounded hover:bg-gray-200 transition">
                <span class="text-sm font-semibold">Cancel</span>
            </a>
        </div>
    </form>
</div>

@endsection
