@extends('layouts.admin.layout')

@section('title', 'Special Fee Assignments')

@section('content')
<div class="relative">
    {{-- Page Header --}}
    <div class="flex flex-wrap lg:flex-row justify-between my-3">
        <div>
            <h1 class="admin-h1 my-3">Special Fee Assignments</h1>
            <p class="text-xs text-gray-500 font-medium">Hostel, transport, and custom fee management for individual students.</p>
        </div>
        <div class="flex items-center gap-2">
            <div class="relative">
                <form method="GET">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search student name..."
                           class="rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-0 w-64 pl-8">
                    <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </form>
            </div>
            <a href="{{ route('finance.special-fees.create') }}"
               class="no-underline text-white px-4 flex items-center custom-green py-2 justify-center shadow-sm hover:shadow-md transition">
                <span class="text-xs font-bold uppercase tracking-wider">Assign Special Fee</span>
            </a>
        </div>
    </div>

    {{-- Navigation --}}
    @include('admin.finance.partials.tabs')

    {{-- Special Fees Table --}}
    <div class="mt-5 bg-white custom-shadow border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Student Details</th>
                        <th class="px-5 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wide">Fee Category</th>
                        <th class="px-5 py-4 text-right text-xs font-bold text-gray-600 uppercase tracking-wide">Amount</th>
                        <th class="px-5 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wide">Due Date</th>
                        <th class="px-5 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wide">Status</th>
                        <th class="px-5 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($fees as $fee)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-gray-900 text-xs">
                                        {{ optional($fee->student->userprofile)->firstname }} {{ optional($fee->student->userprofile)->lastname }}
                                    </span>
                                    <span class="text-[10px] text-gray-400 uppercase font-medium">
                                        {{ optional(optional($fee->student->studentAcademicLatest)->standardLink->standard)->name }}
                                        {{ optional(optional($fee->student->studentAcademicLatest)->standardLink->section)->name }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-xs font-semibold text-gray-700">
                                    {{ $fee->feeCategory->name ?? '—' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right font-bold text-red-700 text-xs">
                                Rs. {{ number_format($fee->amount, 0) }}
                            </td>
                            <td class="px-5 py-4 text-center text-xs text-gray-600 font-medium">
                                {{ $fee->due_date ? $fee->due_date->format('d M, Y') : '—' }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($fee->status == 2)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700 uppercase border border-green-200">Applied</span>
                                @elseif($fee->status == 1)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 uppercase border border-blue-200">Active</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500 uppercase border border-gray-200">Inactive</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="#" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        @include('admin.finance.partials.table-empty', ['message' => 'No special fee assignments found.'])
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($fees->hasPages())
            <div class="px-5 py-4 border-t bg-gray-50/50">
                {{ $fees->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
