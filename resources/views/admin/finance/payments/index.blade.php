@extends('layouts.admin.layout')

@section('title', 'Fee Payments')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">

        <div>

            <h1 class="text-3xl font-bold text-gray-800">
                Fee Payments
            </h1>

            <p class="text-gray-500 mt-1">
                Manage invoices and collect payments
            </p>

        </div>

        {{-- Search --}}
        <form method="GET" class="mt-4 md:mt-0">

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search invoice or student..."
                class="w-80 border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-600 focus:outline-none"
            >

        </form>

    </div>

    {{-- Table --}}
    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">

        <div class="overflow-x-auto">

            <table class="min-w-full text-sm">

                <thead class="bg-gray-100 text-gray-700">

                    <tr>

                        <th class="text-left px-6 py-4 font-semibold">
                            Invoice
                        </th>

                        <th class="text-left px-6 py-4 font-semibold">
                            Student
                        </th>

                        <th class="text-left px-6 py-4 font-semibold">
                            Class
                        </th>

                        <th class="text-left px-6 py-4 font-semibold">
                            Total
                        </th>

                        <th class="text-left px-6 py-4 font-semibold">
                            Paid
                        </th>

                        <th class="text-left px-6 py-4 font-semibold">
                            Balance
                        </th>

                        <th class="text-left px-6 py-4 font-semibold">
                            Status
                        </th>

                        <th class="text-left px-6 py-4 font-semibold">
                            Action
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-gray-200">

                    @forelse($fees as $fee)

                        <tr class="hover:bg-gray-50 transition">

                            {{-- Invoice --}}
                            <td class="px-6 py-4 font-semibold text-blue-700">

                                {{ $fee->invoice_no }}

                            </td>

                            {{-- Student --}}
                            <td class="px-6 py-4">

                                <div class="font-semibold text-gray-800">

                                    {{ $fee->student->userprofile->firstname ?? '' }}
                                    {{ $fee->student->userprofile->lastname ?? '' }}

                                </div>

                                <div class="text-xs text-gray-500 mt-1">

                                    {{ $fee->student->email ?? '' }}

                                </div>

                            </td>

                            {{-- Class --}}
                            <td class="px-6 py-4">

                                {{ $fee->studentAcademic->standardLink->standard->name ?? '-' }}

                                @if($fee->studentAcademic->standardLink->section)

                                    -
                                    {{ $fee->studentAcademic->standardLink->section->name }}

                                @endif

                            </td>

                            {{-- Total --}}
                            <td class="px-6 py-4 font-semibold text-blue-600">

                                ₹{{ number_format($fee->total_amount, 2) }}

                            </td>

                            {{-- Paid --}}
                            <td class="px-6 py-4 font-semibold text-green-600">

                                ₹{{ number_format($fee->paid_amount, 2) }}

                            </td>

                            {{-- Balance --}}
                            <td class="px-6 py-4 font-semibold text-red-600">

                                ₹{{ number_format($fee->balance, 2) }}

                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4">

                                @if($fee->balance <= 0)

                                    <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-700 font-semibold">
                                        Paid
                                    </span>

                                @elseif($fee->paid_amount > 0)

                                    <span class="px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700 font-semibold">
                                        Partial
                                    </span>

                                @else

                                    <span class="px-3 py-1 text-xs rounded-full bg-red-100 text-red-700 font-semibold">
                                        Unpaid
                                    </span>

                                @endif

                            </td>

                            {{-- Action --}}
                            <td class="px-6 py-4">

                                <a href="{{ route('finance.payments.create', $fee->id) }}"
                                   class="bg-red-700 hover:bg-red-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition">

                                    Collect Payment

                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="8" class="text-center py-10 text-gray-500">

                                No invoices found.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    {{-- Pagination --}}
    <div class="mt-6">

        {{ $fees->links() }}

    </div>

</div>

@endsection
