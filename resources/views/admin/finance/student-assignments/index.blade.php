@extends('layouts.admin.layout')

@section('title', 'Student Fee Assignments')

@section('content')

<div class="relative">
    {{-- Header --}}
    <div class="flex flex-wrap lg:flex-row justify-between my-3">
        <div>
            <h1 class="admin-h1 my-3">Student Fee Assignments</h1>
        </div>
        <div class="flex items-center">
            <form method="GET" class="relative">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search student..."
                       class="rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-0 w-64 pl-8">
                <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </form>
        </div>
    </div>

    @include('admin.finance.partials.tabs')

    {{-- Table --}}
    <div class="bg-white custom-shadow border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Student</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Class</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Fee Structure</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Assigned</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Paid</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Balance</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Due Date</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($assignments as $assignment)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 text-xs">
                                    {{ $assignment->student->userprofile->firstname ?? '' }}
                                    {{ $assignment->student->userprofile->lastname ?? '' }}
                                </div>
                                <div class="text-[10px] text-gray-400 mt-0.5">ID: {{ $assignment->student->id ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-600">
                                {{ $assignment->standardLink->standard->name ?? '-' }}
                                @if($assignment->standardLink->section)
                                    - {{ $assignment->standardLink->section->name }}
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs font-medium text-gray-700">
                                {{ $assignment->feeStructure->title ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-blue-700">
                                ₹{{ number_format($assignment->assigned_amount, 0) }}
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-green-700">
                                ₹{{ number_format($assignment->paid_amount, 0) }}
                            </td>
                            <td class="px-6 py-4 text-xs font-bold text-red-600">
                                ₹{{ number_format($assignment->balance, 0) }}
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500">
                                {{ optional($assignment->due_date)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($assignment->balance <= 0)
                                    <span class="px-2 py-0.5 text-[10px] rounded-full bg-green-100 text-green-700 font-bold uppercase tracking-wider">Paid</span>
                                @elseif($assignment->paid_amount > 0)
                                    <span class="px-2 py-0.5 text-[10px] rounded-full bg-yellow-100 text-yellow-700 font-bold uppercase tracking-wider">Partial</span>
                                @else
                                    <span class="px-2 py-0.5 text-[10px] rounded-full bg-red-100 text-red-700 font-bold uppercase tracking-wider">Pending</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-gray-400 text-xs">No student assignments found.</td>
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
