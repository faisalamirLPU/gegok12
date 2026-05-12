@extends('layouts.admin.layout')

@section('title', 'Student Fee Assignments')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">

        <div>

            <h1 class="text-3xl font-bold text-gray-800">
                Student Fee Assignments
            </h1>

            <p class="text-gray-500 mt-1">
                Manage assigned student fee structures
            </p>

        </div>

        {{-- Search --}}
        <form method="GET" class="mt-4 md:mt-0">

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search student..."
                class="w-72 border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-600 focus:outline-none"
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
                            Student
                        </th>

                        <th class="text-left px-6 py-4 font-semibold">
                            Class
                        </th>

                        <th class="text-left px-6 py-4 font-semibold">
                            Fee Structure
                        </th>

                        <th class="text-left px-6 py-4 font-semibold">
                            Assigned
                        </th>

                        <th class="text-left px-6 py-4 font-semibold">
                            Paid
                        </th>

                        <th class="text-left px-6 py-4 font-semibold">
                            Balance
                        </th>

                        <th class="text-left px-6 py-4 font-semibold">
                            Due Date
                        </th>

                        <th class="text-left px-6 py-4 font-semibold">
                            Status
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-gray-200">

                    @forelse($assignments as $assignment)

                        <tr class="hover:bg-gray-50 transition">

                            {{-- Student --}}
                            <td class="px-6 py-4">

                                <div class="font-semibold text-gray-800">

                                    {{ $assignment->student->userprofile->firstname ?? '' }}
                                    {{ $assignment->student->userprofile->lastname ?? '' }}

                                </div>

                                <div class="text-xs text-gray-500 mt-1">

                                    ID:
                                    {{ $assignment->student->id ?? '-' }}

                                </div>

                            </td>

                            {{-- Class --}}
                            <td class="px-6 py-4">

                                {{ $assignment->standardLink->standard->name ?? '-' }}

                                @if($assignment->standardLink->section)

                                    -
                                    {{ $assignment->standardLink->section->name }}

                                @endif

                            </td>

                            {{-- Fee Structure --}}
                            <td class="px-6 py-4">

                                <span class="font-medium text-gray-700">

                                    {{ $assignment->feeStructure->title ?? '-' }}

                                </span>

                            </td>

                            {{-- Assigned --}}
                            <td class="px-6 py-4 text-blue-700 font-semibold">

                                ₹{{ number_format($assignment->assigned_amount, 2) }}

                            </td>

                            {{-- Paid --}}
                            <td class="px-6 py-4 text-green-600 font-semibold">

                                ₹{{ number_format($assignment->paid_amount, 2) }}

                            </td>

                            {{-- Balance --}}
                            <td class="px-6 py-4 text-red-600 font-semibold">

                                ₹{{ number_format($assignment->balance, 2) }}

                            </td>

                            {{-- Due --}}
                            <td class="px-6 py-4">

                                {{ optional($assignment->due_date)->format('d M Y') }}

                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4">

                                @if($assignment->balance <= 0)

                                    <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-700 font-semibold">
                                        Paid
                                    </span>

                                @elseif($assignment->paid_amount > 0)

                                    <span class="px-3 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700 font-semibold">
                                        Partial
                                    </span>

                                @else

                                    <span class="px-3 py-1 text-xs rounded-full bg-red-100 text-red-700 font-semibold">
                                        Pending
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="8" class="text-center py-10 text-gray-500">

                                No student assignments found.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    {{-- Pagination --}}
    <div class="mt-6">

        {{ $assignments->links() }}

    </div>

</div>

@endsection
