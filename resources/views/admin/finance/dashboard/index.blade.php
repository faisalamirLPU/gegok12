@extends('layouts.admin.layout')

@section('content')

<div>

    {{-- Heading --}}
    <div class="mb-4">

        <h1 class="admin-h1 font-plex my-2">
            Finance Dashboard
        </h1>

        <p class="text-gray-500">
            School finance overview
        </p>

    </div>


    {{-- Stats --}}
    <div class="flex flex-wrap my-2">

        {{-- Total Collection --}}
        <div class="w-full lg:w-1/4 md:w-1/2 px-1 my-2">

            <div class="bg-white custom-shadow px-4 py-5 border">

                <div class="text-center">

                    <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto">

                        <i class="fas fa-wallet text-green-500 text-2xl"></i>

                    </div>

                    <h2 class="text-4xl font-bold text-gray-800 mt-3">
                        ₹{{ number_format($totalCollection, 2) }}
                    </h2>

                    <p class="text-gray-500 text-sm mt-1">
                        Total Collection
                    </p>

                </div>

            </div>

        </div>


        {{-- Pending Fees --}}
        <div class="w-full lg:w-1/4 md:w-1/2 px-1 my-2">

            <div class="bg-white custom-shadow px-4 py-5 border">

                <div class="text-center">

                    <div class="w-16 h-16 rounded-full bg-yellow-100 flex items-center justify-center mx-auto">

                        <i class="fas fa-clock text-yellow-500 text-2xl"></i>

                    </div>

                    <h2 class="text-4xl font-bold text-gray-800 mt-3">
                        ₹{{ number_format($pendingFees, 2) }}
                    </h2>

                    <p class="text-gray-500 text-sm mt-1">
                        Pending Fees
                    </p>

                </div>

            </div>

        </div>


        {{-- Overdue --}}
        <div class="w-full lg:w-1/4 md:w-1/2 px-1 my-2">

            <div class="bg-white custom-shadow px-4 py-5 border">

                <div class="text-center">

                    <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto">

                        <i class="fas fa-file-invoice text-red-500 text-2xl"></i>

                    </div>

                    <h2 class="text-4xl font-bold text-gray-800 mt-3">
                        {{ $overdueInvoices }}
                    </h2>

                    <p class="text-gray-500 text-sm mt-1">
                        Overdue Invoices
                    </p>

                </div>

            </div>

        </div>


        {{-- Today Collection --}}
        <div class="w-full lg:w-1/4 md:w-1/2 px-1 my-2">

            <div class="bg-white custom-shadow px-4 py-5 border">

                <div class="text-center">

                    <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center mx-auto">

                        <i class="fas fa-coins text-blue-500 text-2xl"></i>

                    </div>

                    <h2 class="text-4xl font-bold text-gray-800 mt-3">
                        ₹{{ number_format($todayCollection, 2) }}
                    </h2>

                    <p class="text-gray-500 text-sm mt-1">
                        Today Collection
                    </p>

                </div>

            </div>

        </div>

    </div>



    {{-- Recent Transactions --}}
    <div class="bg-white custom-shadow border mt-4">

        <div class="px-5 py-4 border-b">

            <div class="flex items-center justify-between">

                <h2 class="text-xl font-semibold text-gray-800">
                    Recent Transactions
                </h2>

                <a href="{{ route('finance.payments.index') }}"
                   class="theme-button">
                    Add Payment
                </a>

            </div>

        </div>

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead>

                    <tr class="border-b bg-gray-50">

                        <th class="text-left py-3 px-4">
                            Student
                        </th>

                        <th class="text-left py-3 px-4">
                            Class
                        </th>

                        <th class="text-left py-3 px-4">
                            Invoice
                        </th>

                        <th class="text-left py-3 px-4">
                            Amount
                        </th>

                        <th class="text-left py-3 px-4">
                            Status
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($recentTransactions as $transaction)

                        <tr class="border-b hover:bg-gray-50">

                            <td class="py-3 px-4">

                                {{ $transaction->student->name ?? 'N/A' }}

                            </td>

                            <td class="py-3 px-4">

                                {{ $transaction->studentAssignment->standardLink->standard->name ?? '-' }}

                                -

                                {{ $transaction->studentAssignment->standardLink->section->name ?? '-' }}

                            </td>

                            <td class="py-3 px-4">

                                {{ $transaction->invoice_no }}

                            </td>

                            <td class="py-3 px-4 font-semibold text-green-600">

                                ₹{{ number_format($transaction->total_amount, 2) }}

                            </td>

                            <td class="py-3 px-4">

                                @if($transaction->balance <= 0)

                                    <span class="px-3 py-1 rounded-full text-xs bg-green-100 text-green-700">
                                        Paid
                                    </span>

                                @else

                                    <span class="px-3 py-1 rounded-full text-xs bg-red-100 text-red-700">
                                        Pending
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5" class="text-center py-5 text-gray-500">

                                No transactions found

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection
