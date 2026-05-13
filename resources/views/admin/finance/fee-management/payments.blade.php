@extends('layouts.admin.layout')

@php
    $academicYear = \App\Helpers\SiteHelper::getAcademicYear(auth()->user()->school_id);
@endphp

@section('title', 'Fee Payments')

@section('content')
<div class="relative">
    {{-- Page Header --}}
    <div class="flex flex-wrap lg:flex-row justify-between my-3">
        <div>
            <h1 class="admin-h1 my-3">Fee Payments ({{ $academicYear->name ?? 'N/A' }})</h1>
        </div>
    </div>

    {{-- Navigation --}}
    @include('admin.finance.partials.tabs')

    {{-- Summary Cards --}}
    <div class="flex flex-wrap -mx-1 mt-4">
        <div class="w-full lg:w-1/4 md:w-1/2 px-1 my-2">
            <div class="bg-white custom-shadow px-4 py-3 border">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-medium text-gray-500">Total Collection</p>
                    <span class="text-green-500 text-xs">●</span>
                </div>
                <p class="mt-1 text-xl font-bold text-green-600">Rs. {{ number_format($summary['total_collection'] ?? 0, 0) }}</p>
            </div>
        </div>
        <div class="w-full lg:w-1/4 md:w-1/2 px-1 my-2">
            <div class="bg-white custom-shadow px-4 py-3 border">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-medium text-gray-500">Pending</p>
                    <span class="text-red-500 text-xs">●</span>
                </div>
                <p class="mt-1 text-xl font-bold text-red-600">Rs. {{ number_format($summary['pending_collection'] ?? 0, 0) }}</p>
            </div>
        </div>
        <div class="w-full lg:w-1/4 md:w-1/2 px-1 my-2">
            <div class="bg-white custom-shadow px-4 py-3 border">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-medium text-gray-500">Advance</p>
                    <span class="text-blue-500 text-xs">●</span>
                </div>
                <p class="mt-1 text-xl font-bold text-blue-600">Rs. {{ number_format($summary['advance_collection'] ?? 0, 0) }}</p>
            </div>
        </div>
        <div class="w-full lg:w-1/4 md:w-1/2 px-1 my-2">
            <div class="bg-white custom-shadow px-4 py-3 border">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-medium text-gray-500">Invoices</p>
                    <span class="text-gray-500 text-xs">●</span>
                </div>
                <p class="mt-1 text-xl font-bold text-gray-800">{{ number_format($summary['total_invoices'] ?? 0) }}</p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="mt-4 bg-white custom-shadow border px-4 py-3">
        <form method="GET" action="{{ route('finance.fee-management.payments') }}" class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[200px]">
                <input
                    id="search"
                    name="search"
                    value="{{ request('search') }}"
                    type="search"
                    placeholder="Invoice no, student name..."
                    class="w-full px-3 py-1.5 text-sm border rounded focus:outline-none focus:border-blue-500"
                >
            </div>
            <div class="w-40">
                <select
                    id="status"
                    name="status"
                    class="w-full px-3 py-1.5 text-sm border rounded focus:outline-none focus:border-blue-500"
                >
                    <option value="">All Status</option>
                    <option value="paid" @selected(request('status') === 'paid')>Paid</option>
                    <option value="partial" @selected(request('status') === 'partial')>Partial</option>
                    <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                    <option value="advance" @selected(request('status') === 'advance')>Advance</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="no-underline text-white px-6 flex items-center custom-green py-1.5 justify-center">
                    <span class="text-sm font-semibold">Filter</span>
                </button>
                <a href="{{ route('finance.fee-management.payments') }}" class="no-underline text-gray-700 px-6 flex items-center bg-gray-100 border py-1.5 justify-center rounded hover:bg-gray-200">
                    <span class="text-sm font-semibold">Reset</span>
                </a>
            </div>
        </form>
    </div>

    {{-- Invoice Table --}}
    <div class="mt-4 bg-white custom-shadow border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Invoice</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Student</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Class</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Fee Categories</th>
                        <th class="px-3 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wide">Total</th>
                        <th class="px-3 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wide">Paid</th>
                        <th class="px-3 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wide">Balance</th>
                        <th class="px-3 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wide">Advance</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Due Date</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wide">Status</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wide">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($fees as $fee)
                        @php
                            $profile = optional($fee->student)->userprofile;
                            $studentName = trim(($profile->firstname ?? '') . ' ' . ($profile->lastname ?? '')) ?: ($fee->student->name ?? 'N/A');
                            $standard = optional(optional(optional($fee->studentAcademic)->standardLink)->standard)->name;
                            $section = optional(optional(optional($fee->studentAcademic)->standardLink)->section)->name;
                            $classDisplay = trim(($standard ?? '') . ' ' . ($section ?? '')) ?: '—';

                            $statusConfig = [
                                'paid'    => ['bg-green-100', 'text-green-700', 'Paid'],
                                'partial' => ['bg-yellow-100', 'text-yellow-700', 'Partial'],
                                'pending' => ['bg-red-100', 'text-red-700', 'Pending'],
                                'advance' => ['bg-blue-100', 'text-blue-700', 'Advance'],
                            ];
                            $status = strtolower($fee->erp_status ?? 'pending');
                            $statusClass = $statusConfig[$status] ?? ['bg-gray-100', 'text-gray-700', $fee->erp_status ?? 'Pending'];
                        @endphp
                        <tr class="hover:bg-gray-50">
                            {{-- Invoice No --}}
                            <td class="px-3 py-2.5">
                                <div class="font-semibold text-blue-700 text-xs">{{ $fee->invoice_no }}</div>
                                <div class="text-xs text-gray-400">{{ $fee->billing_cycle ?? $fee->payment_period ?? '—' }}</div>
                            </td>

                            {{-- Student --}}
                            <td class="px-3 py-2.5">
                                <div class="font-medium text-gray-900 text-xs">{{ $studentName }}</div>
                                <div class="text-xs text-gray-400">ID: {{ $fee->user_id }}</div>
                            </td>

                            {{-- Class --}}
                            <td class="px-3 py-2.5 text-gray-700 text-xs">{{ $classDisplay }}</td>

                            {{-- Fee Categories --}}
                            <td class="px-3 py-2.5 max-w-[180px]">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($fee->items->take(3) as $item)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700 cursor-help" title="{{ $item->remarks }}">
                                            {{ $item->category->code ?? $item->category->name ?? 'Other' }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-gray-400">No items</span>
                                    @endforelse
                                    @if($fee->items->count() > 3)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-50 text-gray-500">
                                            +{{ $fee->items->count() - 3 }} more
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Total Amount --}}
                            <td class="px-3 py-2.5 text-right font-semibold text-gray-900 text-xs">
                                Rs. {{ number_format($fee->total_amount, 0) }}
                            </td>

                            {{-- Paid Amount --}}
                            <td class="px-3 py-2.5 text-right font-semibold text-green-600 text-xs">
                                Rs. {{ number_format($fee->paid_amount, 0) }}
                            </td>

                            {{-- Balance --}}
                            <td class="px-3 py-2.5 text-right font-semibold text-red-600 text-xs">
                                Rs. {{ number_format($fee->balance_amount, 0) }}
                            </td>

                            {{-- Advance --}}
                            <td class="px-3 py-2.5 text-right font-semibold text-blue-600 text-xs">
                                Rs. {{ number_format($fee->advance_amount, 0) }}
                            </td>

                            {{-- Due Date --}}
                            <td class="px-3 py-2.5 text-gray-600">
                                <div class="text-xs">
                                    @if($fee->due_date)
                                        {{ $fee->due_date->format('d M Y') }}
                                        @if($fee->due_date->isPast() && $fee->erp_status !== 'Paid')
                                            <span class="text-red-500 ml-1">⚠</span>
                                        @endif
                                    @else
                                        —
                                    @endif
                                </div>
                            </td>

                            {{-- Status Badge --}}
                            <td class="px-3 py-2.5 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $statusClass[0] }} {{ $statusClass[1] }}">
                                    {{ $statusClass[2] }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-3 py-2.5 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('finance.payments.create', $fee->id) }}"
                                       class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition"
                                       title="Record Payment">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('finance.fee-records.show', $fee->user_id) }}"
                                       class="p-1.5 text-gray-600 hover:bg-gray-100 rounded transition"
                                       title="View Student">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-4 py-12 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="font-medium text-sm text-gray-700">No invoices found</p>
                                <p class="text-xs text-gray-400 mt-1">Try adjusting your search or filter criteria</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($fees->hasPages())
            <div class="px-4 py-3 border-t flex items-center justify-between">
                <div class="text-xs text-gray-500">
                    Showing {{ $fees->firstItem() ?? 0 }} to {{ $fees->lastItem() ?? 0 }} of {{ $fees->total() }} results
                </div>
                <div>
                    {{ $fees->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection