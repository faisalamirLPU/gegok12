@extends('layouts.admin.layout')

@section('title', 'Payment Receipts')

@section('content')

    <div class="relative">

        <div class="flex items-center justify-between my-4">

            <div>

                <h1 class="admin-h1">
                    Payment Receipts
                </h1>

                <p class="text-sm text-gray-500 mt-1">
                    Invoice:
                    {{ $fee->invoice_no }}
                </p>

            </div>

        </div>

        <div class="bg-white custom-shadow border overflow-hidden">

            <table class="w-full">

                <thead>

                    <tr class="bg-gray-50 border-b">

                        <th class="px-4 py-3 text-left text-xs font-bold uppercase">
                            Receipt No
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-bold uppercase">
                            Date
                        </th>

                        <th class="px-4 py-3 text-left text-xs font-bold uppercase">
                            Method
                        </th>

                        <th class="px-4 py-3 text-right text-xs font-bold uppercase">
                            Amount
                        </th>

                        <th class="px-4 py-3 text-center text-xs font-bold uppercase">
                            Action
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($fee->payments as $payment)

                        <tr class="border-b">

                            <td class="px-4 py-3 text-sm font-mono">
                                {{ $payment->receipt_no }}
                            </td>

                            <td class="px-4 py-3 text-sm">
                                {{ optional($payment->payment_date)->format('d M Y') }}
                            </td>

                            <td class="px-4 py-3 text-sm uppercase">
                                {{ $payment->payment_method }}
                            </td>

                            <td class="px-4 py-3 text-sm text-right font-bold text-green-700">
                                Rs. {{ number_format($payment->amount, 2) }}
                            </td>

                            <td class="px-4 py-3 text-center">

                                <a href="{{ route('finance.receipt', $payment->id)}}" target="_blank"
                                    class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-700 border border-blue-100 rounded hover:bg-blue-100 transition">

                                    <span class="text-xs font-bold uppercase">
                                        View Receipt
                                    </span>

                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5" class="px-4 py-10 text-center text-gray-400">

                                No receipts found.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

@endsection