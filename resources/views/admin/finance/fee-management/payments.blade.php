@extends('layouts.admin.layout')

@section('title', 'Fee Payments')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Fee Payments</h1>
        <p class="text-gray-600 mt-1">ERP invoice ledger with fee category breakdowns.</p>
    </div>

    @include('admin.finance.partials.navigation')

    <div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-sm font-medium text-gray-500">Total Collection</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">Rs. {{ number_format($summary['total_collection'] ?? 0, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-sm font-medium text-gray-500">Pending Collection</p>
            <p class="mt-2 text-2xl font-bold text-red-600">Rs. {{ number_format($summary['pending_collection'] ?? 0, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-sm font-medium text-gray-500">Advance Collection</p>
            <p class="mt-2 text-2xl font-bold text-blue-600">Rs. {{ number_format($summary['advance_collection'] ?? 0, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-sm font-medium text-gray-500">Total Invoices</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($summary['total_invoices'] ?? 0) }}</p>
        </div>
    </div>

    <div class="mt-6 bg-white rounded-lg border border-gray-200 p-4">
        <form method="GET" action="{{ route('finance.fee-management.payments') }}" class="grid grid-cols-1 gap-3 md:grid-cols-12">
            <div class="md:col-span-7">
                <label for="search" class="sr-only">Search</label>
                <input
                    id="search"
                    name="search"
                    value="{{ request('search') }}"
                    type="search"
                    placeholder="Search invoice, student, or fee category"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
            </div>

            <div class="md:col-span-3">
                <label for="status" class="sr-only">Status</label>
                <select
                    id="status"
                    name="status"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                >
                    <option value="">All Statuses</option>
                    <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                    <option value="partial" @selected(request('status') === 'partial')>Partial</option>
                    <option value="paid" @selected(request('status') === 'paid')>Paid</option>
                    <option value="advance" @selected(request('status') === 'advance')>Advance</option>
                </select>
            </div>

            <div class="md:col-span-2 flex gap-2">
                <button type="submit" class="w-full rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                    Filter
                </button>
                <a href="{{ route('finance.fee-management.payments') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden mt-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Invoice No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Student Name</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Class &amp; Section</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Fee Categories</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-600">Total Amount</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-600">Paid Amount</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-600">Balance</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-600">Advance</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Due Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-600">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($fees as $fee)
                        @php
                            $profile = optional($fee->student)->userprofile;
                            $studentName = trim(($profile->firstname ?? '') . ' ' . ($profile->lastname ?? '')) ?: ($fee->student->name ?? '-');
                            $standard = optional(optional(optional($fee->studentAcademic)->standardLink)->standard)->name;
                            $section = optional(optional(optional($fee->studentAcademic)->standardLink)->section)->name;
                            $statusClass = [
                                'Paid' => 'bg-green-100 text-green-700',
                                'Partial' => 'bg-yellow-100 text-yellow-800',
                                'Pending' => 'bg-red-100 text-red-700',
                                'Advance' => 'bg-blue-100 text-blue-700',
                            ][$fee->erp_status] ?? 'bg-gray-100 text-gray-700';
                        @endphp

                        <tr class="align-top hover:bg-gray-50">
                            <td class="px-4 py-4">
                                <div class="font-semibold text-blue-700">{{ $fee->invoice_no }}</div>
                                <div class="text-xs text-gray-500">{{ $fee->billing_cycle ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-4 font-medium text-gray-900">{{ $studentName }}</td>
                            <td class="px-4 py-4 text-sm text-gray-700">{{ trim(($standard ?? '') . ' ' . ($section ?? '')) ?: '-' }}</td>
                            <td class="px-4 py-4">
                                <div class="space-y-2">
                                    @forelse($fee->items as $item)
                                        <div class="rounded-md bg-gray-50 px-3 py-2">
                                            <div class="flex items-center justify-between gap-3">
                                                <span class="text-sm font-medium text-gray-900">{{ $item->category->name ?? 'Uncategorized' }}</span>
                                                <span class="text-sm font-semibold text-gray-900">Rs. {{ number_format($item->total, 2) }}</span>
                                            </div>
                                            @if((float) $item->fine_amount > 0)
                                                <div class="mt-1 text-xs text-gray-500">
                                                    Base Rs. {{ number_format($item->amount, 2) }} + Fine Rs. {{ number_format($item->fine_amount, 2) }}
                                                </div>
                                            @endif
                                        </div>
                                    @empty
                                        <span class="text-sm text-gray-400">No fee items</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-4 py-4 text-right font-semibold text-gray-900">Rs. {{ number_format($fee->total_amount, 2) }}</td>
                            <td class="px-4 py-4 text-right font-semibold text-green-700">Rs. {{ number_format($fee->paid_amount, 2) }}</td>
                            <td class="px-4 py-4 text-right font-semibold text-red-600">Rs. {{ number_format($fee->balance_amount, 2) }}</td>
                            <td class="px-4 py-4 text-right font-semibold text-blue-600">Rs. {{ number_format($fee->advance_amount, 2) }}</td>
                            <td class="px-4 py-4 text-sm text-gray-700">{{ optional($fee->due_date)->format('d M Y') ?? '-' }}</td>
                            <td class="px-4 py-4">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                    {{ $fee->erp_status }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <a href="{{ route('finance.payments.create', $fee->id) }}" class="text-sm font-semibold text-blue-700 hover:text-blue-900">
                                    Record Payment
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-6 py-12 text-center text-gray-500">
                                No invoices found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 px-4 py-3">
            {{ $fees->links() }}
        </div>
    </div>
</div>
@endsection
