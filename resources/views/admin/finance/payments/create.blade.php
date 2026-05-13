@extends('layouts.admin.layout')

@section('title', 'Collect Payment')

@section('content')

<div class="relative">

    {{-- Header --}}
    <div class="flex flex-wrap lg:flex-row justify-between my-3">

        <div>

            <h1 class="admin-h1 my-3">
                Collect Payment
            </h1>

        </div>

        <div class="flex items-center gap-2">

            {{-- Latest Receipt --}}
            @if($fee->payments()->exists())

                <a
                    href="{{ route('finance.receipt', $fee->payments()->latest()->first()->id) }}"
                    target="_blank"
                    class="inline-flex items-center px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs font-bold uppercase tracking-wider transition"
                >

                    Latest Receipt

                </a>

            @endif

            {{-- Back --}}
            <a
                href="{{ route('finance.payments.index') }}"
                class="no-underline text-gray-600 px-4 flex items-center bg-gray-100 border rounded py-1.5 justify-center hover:bg-gray-200 transition"
            >

                <span class="text-[10px] font-bold uppercase tracking-wider">

                    Back to Payments

                </span>

            </a>

        </div>

    </div>

    {{-- Tabs --}}
    @include('admin.finance.partials.tabs')

    {{-- LOCKED WARNING --}}
    @if($fee->is_locked)

        <div class="mt-6 bg-red-50 border border-red-200 rounded-lg p-5">

            <div class="flex items-start gap-3">

                <div class="flex-shrink-0">

                    <svg
                        class="w-6 h-6 text-red-600"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8v4m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"
                        />

                    </svg>

                </div>

                <div>

                    <h2 class="text-sm font-bold text-red-700 uppercase tracking-wide">

                        Invoice Locked

                    </h2>

                    <p class="text-sm text-red-600 mt-1 leading-relaxed">

                        This invoice has been locked by administration.
                        No additional payment can be collected or modified
                        unless the invoice is unlocked.

                    </p>

                </div>

            </div>

        </div>

    @endif

    {{-- Form --}}
    <form
        method="POST"
        action="{{ route('finance.payments.store', $fee->id) }}"
        class="bg-white custom-shadow border overflow-hidden mt-6"
    >

        @csrf

        {{-- Invoice Summary --}}
        <div class="border-b p-5 bg-gray-50/50">

            <div class="flex items-center justify-between mb-4">

                <h2 class="text-sm font-semibold text-gray-800 uppercase tracking-wider">

                    Invoice Summary

                </h2>

                @if($fee->is_locked)

                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold bg-gray-200 text-gray-700 uppercase tracking-wider">

                        Locked

                    </span>

                @endif

            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Invoice --}}
                <div>

                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">

                        Invoice Number

                    </label>

                    <div class="w-full border rounded px-4 py-2 bg-white text-sm font-semibold text-blue-700 font-mono">

                        {{ $fee->invoice_no }}

                    </div>

                </div>

                {{-- Balance --}}
                <div>

                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">

                        Outstanding Balance

                    </label>

                    <div class="w-full border border-red-100 rounded px-4 py-2 bg-red-50 text-red-700 text-sm font-bold">

                        Rs. {{ number_format($fee->balance, 0) }}

                    </div>

                </div>

            </div>

        </div>

        {{-- Payment Information --}}
        <div class="p-5">

            <h2 class="text-sm font-semibold text-gray-800 uppercase tracking-wider mb-4">

                Transaction Details

            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Amount --}}
                <div>

                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">

                        Collection Amount

                        <span class="text-red-500">*</span>

                    </label>

                    <input
                        type="number"
                        step="0.01"
                        name="amount"
                        required
                        placeholder="Enter amount to collect"

                        {{ $fee->is_locked ? 'disabled' : '' }}

                        class="w-full border border-gray-300 rounded px-4 py-2 text-sm focus:border-blue-500 focus:ring-0
                        {{ $fee->is_locked ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                    >

                </div>

                {{-- Method --}}
                <div>

                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">

                        Payment Method

                        <span class="text-red-500">*</span>

                    </label>

                    <select
                        name="payment_method"

                        {{ $fee->is_locked ? 'disabled' : '' }}

                        class="w-full border border-gray-300 rounded px-4 py-2 text-sm focus:border-blue-500 focus:ring-0
                        {{ $fee->is_locked ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                    >

                        <option value="cash">
                            Cash Payment
                        </option>

                        <option value="upi">
                            UPI / Online
                        </option>

                        <option value="bank">
                            Bank Transfer
                        </option>

                        <option value="cheque">
                            Cheque
                        </option>

                    </select>

                </div>

                {{-- Transaction --}}
                <div class="md:col-span-2">

                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">

                        Transaction Reference

                    </label>

                    <input
                        type="text"
                        name="transaction_id"
                        placeholder="Enter transaction ID or cheque number"

                        {{ $fee->is_locked ? 'disabled' : '' }}

                        class="w-full border border-gray-300 rounded px-4 py-2 text-sm focus:border-blue-500 focus:ring-0
                        {{ $fee->is_locked ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                    >

                </div>

                {{-- Remarks --}}
                <div class="md:col-span-2">

                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">

                        Administrative Remarks

                    </label>

                    <textarea
                        name="remarks"
                        rows="2"
                        placeholder="Any additional notes about this payment..."

                        {{ $fee->is_locked ? 'disabled' : '' }}

                        class="w-full border border-gray-300 rounded px-4 py-2 text-sm focus:border-blue-500 focus:ring-0
                        {{ $fee->is_locked ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                    ></textarea>

                </div>

            </div>

        </div>

        {{-- Footer --}}
        <div class="bg-gray-50 border-t p-5 flex items-center gap-3">

            @if(!$fee->is_locked)

                {{-- Submit --}}
                <button
                    type="submit"
                    class="no-underline text-white px-8 flex items-center custom-green py-2 justify-center shadow-sm hover:shadow-md transition"
                >

                    <span class="text-sm font-semibold">

                        Post Payment

                    </span>

                </button>

            @else

                {{-- Disabled --}}
                <button
                    type="button"
                    disabled
                    class="no-underline text-white px-8 flex items-center bg-gray-400 py-2 justify-center rounded cursor-not-allowed"
                >

                    <span class="text-sm font-semibold">

                        Invoice Locked

                    </span>

                </button>

            @endif

            {{-- Cancel --}}
            <a
                href="{{ url()->previous() }}"
                class="no-underline text-gray-700 px-6 flex items-center bg-gray-100 border py-2 justify-center rounded hover:bg-gray-200 transition"
            >

                <span class="text-sm font-semibold">

                    Cancel

                </span>

            </a>

        </div>

    </form>

</div>

@endsection