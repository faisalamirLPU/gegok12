@extends('layouts.admin.layout')

@section('title', 'Student Fee Assignments')

@section('content')

<div class="relative">

    {{-- Header --}}
    @include('admin.finance.components.header', [
        'title' => 'Student Fee Assignments',
        'subtitle' => 'Manage assigned student fee structures'
    ])


    {{-- Search + Filters --}}
    <div class="flex flex-wrap items-center justify-between mb-4">

        {{-- Status Filters --}}
        <div class="flex flex-wrap gap-2">

            <button class="border bg-white px-5 py-2 shadow-sm text-gray-700 status-filter"
                    data-filter="">

                ALL

            </button>

            <button class="border bg-white px-5 py-2 shadow-sm text-gray-700 status-filter"
                    data-filter="paid">

                PAID

            </button>

            <button class="border bg-white px-5 py-2 shadow-sm text-gray-700 status-filter"
                    data-filter="pending">

                PENDING

            </button>

        </div>


        {{-- Search --}}
        <div class="mt-3 md:mt-0">

            <input type="text"
                   id="searchInput"
                   placeholder="Search student, email, class..."
                   class="border px-4 py-2 bg-white w-80 focus:outline-none">

        </div>

    </div>


    {{-- Table --}}
    <div class="bg-white border border-gray-200 rounded shadow-sm overflow-hidden">

        <div class="overflow-x-auto tableFixHead"
             style="max-height:550px;">

            <table class="w-full">

                <thead class="bg-gray-100 border-b">

                    <tr>

                        <th class="text-left px-4 py-3 font-semibold text-gray-700">
                            Student
                        </th>

                        <th class="text-left px-4 py-3 font-semibold text-gray-700">
                            Class
                        </th>

                        <th class="text-left px-4 py-3 font-semibold text-gray-700">
                            Fee Structure
                        </th>

                        <th class="text-left px-4 py-3 font-semibold text-gray-700">
                            Assigned Amount
                        </th>

                        <th class="text-left px-4 py-3 font-semibold text-gray-700">
                            Paid
                        </th>

                        <th class="text-left px-4 py-3 font-semibold text-gray-700">
                            Balance
                        </th>

                        <th class="text-left px-4 py-3 font-semibold text-gray-700">
                            Due Date
                        </th>

                        <th class="text-left px-4 py-3 font-semibold text-gray-700 text-center">
                            Status
                        </th>

                    </tr>

                </thead>


                <tbody id="assignmentTable">

                    @forelse($assignments as $assignment)

                        <tr class="border-b hover:bg-gray-50 assignment-row">

                            {{-- Student --}}
                            <td class="px-4 py-4 student-data">

                                <div class="font-semibold text-gray-800">

                                    {{
                                        ($assignment->student->userprofile->firstname ?? '')
                                        . ' ' .
                                        ($assignment->student->userprofile->lastname ?? '')
                                    }}

                                </div>

                                <div class="text-sm text-gray-500">

                                    {{ $assignment->student->email ?? '' }}

                                </div>

                            </td>


                            {{-- Class --}}
                            <td class="px-4 py-4 class-data">

                                {{ $assignment->standardLink->standard->name ?? '-' }}

                                -

                                {{ $assignment->standardLink->section->name ?? '-' }}

                            </td>


                            {{-- Fee Structure --}}
                            <td class="px-4 py-4">

                                {{ $assignment->feeStructure->title ?? 'Fee Structure' }}

                            </td>


                            {{-- Assigned Amount --}}
                            <td class="px-4 py-4 font-semibold text-gray-800">

                                ₹{{ number_format($assignment->assigned_amount, 2) }}

                            </td>


                            {{-- Paid --}}
                            <td class="px-4 py-4 text-green-600 font-semibold">

                                ₹{{ number_format($assignment->paid_amount, 2) }}

                            </td>


                            {{-- Balance --}}
                            <td class="px-4 py-4 text-red-500 font-semibold">

                                ₹{{ number_format($assignment->balance, 2) }}

                            </td>


                            {{-- Due Date --}}
                            <td class="px-4 py-4">

                                {{ optional($assignment->due_date)->format('d M Y') }}

                            </td>


                            {{-- Status --}}
                            <td class="px-4 py-4 status-value">

                                @if($assignment->status == 1)

                                    <div class="flex justify-center">

                                        <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-700">
                                            Paid
                                        </span>

                                    </div>

                                @else

                                    <div class="flex justify-center">

                                        <span class="px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">
                                            Pending
                                        </span>

                                    </div>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="8"
                                class="text-center py-5 text-gray-500">

                                No Assignments Found

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- Pagination --}}
    <div class="mt-4">

        {{ $assignments->links() }}

    </div>

</div>



{{-- Dynamic Search + Filter --}}
<script>

document.addEventListener('DOMContentLoaded', function () {

    const searchInput =
        document.getElementById('searchInput');

    const rows =
        document.querySelectorAll('.assignment-row');

    const filterButtons =
        document.querySelectorAll('.status-filter');

    let activeFilter = '';

    function filterTable() {

        const value =
            searchInput.value.toLowerCase();

        rows.forEach(row => {

            const student =
                row.querySelector('.student-data')
                   .innerText
                   .toLowerCase();

            const className =
                row.querySelector('.class-data')
                   .innerText
                   .toLowerCase();

            const status =
                row.querySelector('.status-value')
                   .innerText
                   .toLowerCase();

            const matchesSearch =
                student.includes(value) ||
                className.includes(value);

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
