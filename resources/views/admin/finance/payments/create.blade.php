@extends('layouts.admin.layout')

@section('title', 'Collect Payment')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="mb-8">

        <h1 class="text-3xl font-bold text-gray-800">
            Collect Payment
        </h1>

        <p class="text-gray-600 mt-2">
            Submit invoice payment for student fees
        </p>

    </div>


    {{-- Alerts --}}
    @include('admin.finance.partials.alerts')


    {{-- Form --}}
    <form method="POST"
          action="{{ route('finance.payments.store', $fee->id) }}"
          class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">

        @csrf


        {{-- Invoice Information --}}
        <div class="border-b border-gray-200 p-6 bg-gray-50">

            <h2 class="text-xl font-semibold text-gray-800 mb-6">
                Invoice Information
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Invoice Number --}}
                <div>

                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Invoice Number
                    </label>

                    <div class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white text-gray-800 font-medium">

                        {{ $fee->invoice_no }}

                    </div>

                </div>


                {{-- Balance --}}
                <div>

                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Remaining Balance
                    </label>

                    <div class="w-full border border-red-200 rounded-lg px-4 py-3 bg-red-50 text-red-700 font-bold">

                        ₹{{ number_format($fee->balance, 2) }}

                    </div>

                </div>

            </div>

        </div>


        {{-- Payment Information --}}
        <div class="p-6">

            <h2 class="text-xl font-semibold text-gray-800 mb-6">
                Payment Information
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Amount --}}
                <div>

                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Amount
                    </label>

                    <input type="number"
                           step="0.01"
                           name="amount"
                           required
                           placeholder="Enter payment amount"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-600 focus:outline-none">

                </div>


                {{-- Payment Method --}}
                <div>

                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Payment Method
                    </label>

                    <select name="payment_method"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-600 focus:outline-none">

                        <option value="cash">
                            Cash
                        </option>

                        <option value="upi">
                            UPI
                        </option>

                        <option value="bank">
                            Bank
                        </option>

                    </select>

                </div>


                {{-- Transaction ID --}}
                <div class="md:col-span-2">

                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Transaction ID
                    </label>

                    <input type="text"
                           name="transaction_id"
                           placeholder="Enter transaction reference ID"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-600 focus:outline-none">

                </div>


                {{-- Remarks --}}
                <div class="md:col-span-2">

                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Remarks
                    </label>

                    <textarea name="remarks"
                              rows="5"
                              placeholder="Enter payment remarks..."
                              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-600 focus:outline-none"></textarea>

                </div>

            </div>

        </div>


        {{-- Footer --}}
        <div class="bg-gray-50 border-t border-gray-200 p-6 flex items-center gap-4">

            <button type="submit"
                    class="bg-red-700 hover:bg-red-800 text-white px-6 py-3 rounded-lg font-semibold shadow transition">

                Submit Payment

            </button>

            <a href="{{ url()->previous() }}"
               class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold transition">

                Cancel

            </a>

        </div>

    </form>

</div>

@endsection
