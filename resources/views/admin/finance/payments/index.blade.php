@extends('layouts.admin.layout')

@section('title', 'Fee Invoices')

@section('content')

<div class="relative">

    {{-- Header --}}
    <div class="flex flex-row justify-between">

        <div>

            <h1 class="admin-h1 my-3">
                Fee Invoices
            </h1>

        </div>

    </div>


    {{-- Success Message --}}
    @if(session('success'))

        <div class="mb-4 px-4 py-3 bg-green-100 border border-green-300 text-green-700 rounded">

            {{ session('success') }}

        </div>

    @endif


    {{-- Search + Filters --}}
    <div class="flex flex-wrap items-center justify-between mb-4">

        {{-- Status Filters --}}
        <div class="flex flex-wrap gap-2">

            <button class="border bg-white px-5 py-2 shadow-sm text-gray-700 status-filter"
                    data-filter="">

                ALL

            </button>

            <button class="border bg-white px-5 py-2 shadow-sm text-gray-700 status-filter"
                    data-filter="unpaid">

                UNPAID

            </button>

            <button class="border bg-white px-5 py-2 shadow-sm text-gray-700 status-filter"
                    data-filter="partial">

                PARTIAL

            </button>

            <button class="border bg-white px-5 py-2 shadow-sm text-gray-700 status-filter"
                    data-filter="paid">

                PAID

            </button>

        </div>


        {{-- Search --}}
        <div class="mt-3 md:mt-0">

            <input type="text"
                   id="searchInput"
                   placeholder="Search invoice or student..."
                   class="border px-4 py-2 bg-white w-80 focus:outline-none">

        </div>

    </div>


    {{-- Table --}}
    <div class="flex flex-row justify-between custom-table overflow-x-auto tableFixHead"
         style="max-height:550px;">

        <table class="w-full">

            <thead class="bg-grey-light">

                <tr class="border-t-2 border-b-2">

                    <th class="text-left text-sm px-2 py-2 text-grey-darker">
                        Invoice
                    </th>

                    <th class="text-left text-sm px-2 py-2 text-grey-darker">
                        Student
                    </th>

                    <th class="text-left text-sm px-2 py-2 text-grey-darker">
                        Class
                    </th>

                    <th class="text-left text-sm px-2 py-2 text-grey-darker">
                        Total
                    </th>

                    <th class="text-left text-sm px-2 py-2 text-grey-darker">
                        Paid
                    </th>

                    <th class="text-left text-sm px-2 py-2 text-grey-darker">
                        Balance
                    </th>

                    <th class="text-left text-sm px-2 py-2 text-grey-darker text-center">
                        Status
                    </th>

                    <th class="text-left text-sm px-2 py-2 text-grey-darker">
                        Action
                    </th>

                </tr>

            </thead>


            @if(count($fees) != 0)

                <tbody class="bg-grey-light"
                       id="invoiceTable">

                    @foreach($fees as $fee)

                        <tr class="border-t-2 border-b-2 invoice-row">

                            {{-- Invoice --}}
                            <td class="py-3 px-2 invoice-data">

                                {{ $fee->invoice_no }}

                            </td>


                            {{-- Student --}}
                            <td class="py-3 px-2 student-data">

                                <div class="font-semibold text-gray-800">

                                    {{
                                        ($fee->student->userprofile->firstname ?? '')
                                        . ' ' .
                                        ($fee->student->userprofile->lastname ?? '')
                                    }}

                                </div>

                                <div class="text-sm text-gray-500">

                                    {{ $fee->student->email ?? '' }}

                                </div>

                            </td>


                            {{-- Class --}}
                            <td class="py-3 px-2">

                                {{ $fee->studentAcademic->standardLink->standard->name ?? '-' }}

                                -

                                {{ $fee->studentAcademic->standardLink->section->name ?? '-' }}

                            </td>


                            {{-- Total --}}
                            <td class="py-3 px-2 font-semibold text-gray-800">

                                ₹{{ number_format($fee->total_amount, 2) }}

                            </td>


                            {{-- Paid --}}
                            <td class="py-3 px-2 text-green-600 font-semibold">

                                ₹{{ number_format($fee->paid_amount, 2) }}

                            </td>


                            {{-- Balance --}}
                            <td class="py-3 px-2 font-semibold">

    @php

        $balance =
            $fee->total_amount - $fee->paid_amount;

    @endphp


    @if($balance > 0)

        {{-- Remaining Due --}}
        <span class="text-red-500">

            ₹{{ number_format($balance, 2) }}

        </span>

    @elseif($balance < 0)

        {{-- Advance Amount --}}
        <span class="text-blue-600">

            Advance ₹{{ number_format(abs($balance), 2) }}

        </span>

    @else

        {{-- Fully Paid --}}
        <span class="text-green-600">

            ₹0.00

        </span>

    @endif

</td>


                            {{-- Status --}}
                            <td class="py-3 px-2 status-data">

    @if($fee->paid_amount == 0)

        <div class="flex justify-center">

            <span class="px-3 py-1 text-xs rounded-full bg-red-100 text-red-700">
                Unpaid
            </span>

        </div>

    @elseif($fee->paid_amount < $fee->total_amount)

        <div class="flex justify-center">

            <span class="px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">
                Partial
            </span>

        </div>

    @elseif($fee->paid_amount == $fee->total_amount)

        <div class="flex justify-center">

            <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-700">
                Paid
            </span>

        </div>

    @else

        <div class="flex justify-center">

            <span class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-700">
                Advance
            </span>

        </div>

    @endif

</td>


                            {{-- Action --}}
                            <td class="py-3 px-2">

                                <a href="{{ route('finance.payments.create', $fee->id) }}"
                                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">

                                    Collect Payment

                                </a>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            @else

                <tbody class="bg-grey-light">

                    <tr class="border-t-2 border-b-2">

                        <td colspan="8"
                            class="py-3 px-2 text-center">

                            No Fee Invoices Found

                        </td>

                    </tr>

                </tbody>

            @endif

        </table>

    </div>


    {{-- Pagination --}}
    <div class="mt-4">

        {{ $fees->links() }}

    </div>

</div>



{{-- Dynamic Search + Filter --}}
<script>

document.addEventListener('DOMContentLoaded', function () {

    const searchInput =
        document.getElementById('searchInput');

    const rows =
        document.querySelectorAll('.invoice-row');

    const filterButtons =
        document.querySelectorAll('.status-filter');

    let activeFilter = '';

    function filterTable() {

        const value =
            searchInput.value.toLowerCase();

        rows.forEach(row => {

            const invoice =
                row.querySelector('.invoice-data')
                   .innerText
                   .toLowerCase();

            const student =
                row.querySelector('.student-data')
                   .innerText
                   .toLowerCase();

            const status =
                row.querySelector('.status-data')
                   .innerText
                   .toLowerCase();

            const matchesSearch =
                invoice.includes(value) ||
                student.includes(value);

            const matchesFilter =
                activeFilter === ''
                    ? true
                    : status.includes(activeFilter);

            row.style.display =
                matchesSearch && matchesFilter
                    ? ''
                    : 'none';

        });

    }

    searchInput.addEventListener('keyup', filterTable);

    filterButtons.forEach(button => {

        button.addEventListener('click', function () {

            activeFilter =
                this.dataset.filter;

            filterTable();

        });

    });

});

</script>

@endsection
