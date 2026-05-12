@extends('layouts.admin.layout')

@section('title', 'Fee Management Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Fee Management</h1>
        <p class="text-gray-600 mt-1">Production ERP finance overview for {{ $academicYear->name }}</p>
    </div>

    @include('admin.finance.partials.navigation')

    <div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 xl:grid-cols-6">
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-sm font-medium text-gray-500">Total Collection</p>
            <p class="mt-2 text-2xl font-bold text-green-700">Rs. {{ number_format($stats['total_collection'], 2) }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-sm font-medium text-gray-500">Pending Collection</p>
            <p class="mt-2 text-2xl font-bold text-red-600">Rs. {{ number_format($stats['pending_collection'], 2) }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-sm font-medium text-gray-500">Advance Collection</p>
            <p class="mt-2 text-2xl font-bold text-blue-600">Rs. {{ number_format($stats['advance_collection'], 2) }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-sm font-medium text-gray-500">Total Invoices</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($stats['total_invoices']) }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-sm font-medium text-gray-500">Partial Payments</p>
            <p class="mt-2 text-2xl font-bold text-yellow-700">{{ number_format($stats['partial_payments']) }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <p class="text-sm font-medium text-gray-500">Paid Invoices</p>
            <p class="mt-2 text-2xl font-bold text-green-700">{{ number_format($stats['paid_invoices']) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 mt-6 md:grid-cols-4">
        <a href="{{ route('finance.fee-management.categories') }}" class="rounded-lg border border-gray-200 bg-white p-5 hover:border-blue-400">
            <p class="text-sm font-medium text-gray-500">Fee Categories</p>
            <p class="mt-2 text-xl font-bold text-gray-900">{{ $stats['active_categories'] }} active / {{ $stats['total_categories'] }} total</p>
        </a>
        <a href="{{ route('finance.fee-management.structures') }}" class="rounded-lg border border-gray-200 bg-white p-5 hover:border-blue-400">
            <p class="text-sm font-medium text-gray-500">Fee Structures</p>
            <p class="mt-2 text-xl font-bold text-gray-900">{{ number_format($stats['total_structures']) }}</p>
        </a>
        <a href="{{ route('finance.fee-management.special-fees') }}" class="rounded-lg border border-gray-200 bg-white p-5 hover:border-blue-400">
            <p class="text-sm font-medium text-gray-500">Special Fees</p>
            <p class="mt-2 text-xl font-bold text-gray-900">{{ number_format($stats['total_special_fees']) }}</p>
        </a>
        <a href="{{ route('finance.fee-management.payments', ['status' => 'pending']) }}" class="rounded-lg border border-gray-200 bg-white p-5 hover:border-blue-400">
            <p class="text-sm font-medium text-gray-500">Pending Payments</p>
            <p class="mt-2 text-xl font-bold text-gray-900">{{ number_format($stats['pending_payments']) }}</p>
        </a>
    </div>

    <div class="grid grid-cols-1 gap-6 mt-6 lg:grid-cols-2">
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Recent Fee Invoices</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-600">Invoice</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-600">Student</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase text-gray-600">Amount</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-600">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($recentFees as $fee)
                            @php
                                $profile = optional($fee->student)->userprofile;
                                $studentName = trim(($profile->firstname ?? '') . ' ' . ($profile->lastname ?? '')) ?: '-';
                            @endphp
                            <tr>
                                <td class="px-5 py-3 text-sm font-semibold text-blue-700">{{ $fee->invoice_no }}</td>
                                <td class="px-5 py-3 text-sm text-gray-900">{{ $studentName }}</td>
                                <td class="px-5 py-3 text-sm font-semibold text-right">Rs. {{ number_format($fee->total_amount, 2) }}</td>
                                <td class="px-5 py-3 text-sm">{{ $fee->erp_status }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-center text-gray-500">No recent invoices.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Recent Special Fees</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-600">Student</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-600">Category</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase text-gray-600">Amount</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-600">Due Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($recentSpecialFees as $special)
                            @php
                                $profile = optional($special->student)->userprofile;
                                $studentName = trim(($profile->firstname ?? '') . ' ' . ($profile->lastname ?? '')) ?: '-';
                            @endphp
                            <tr>
                                <td class="px-5 py-3 text-sm text-gray-900">{{ $studentName }}</td>
                                <td class="px-5 py-3 text-sm text-gray-900">{{ $special->feeCategory->name ?? '-' }}</td>
                                <td class="px-5 py-3 text-sm font-semibold text-right">Rs. {{ number_format($special->amount, 2) }}</td>
                                <td class="px-5 py-3 text-sm text-gray-700">{{ optional($special->due_date)->format('d M Y') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-center text-gray-500">No recent special fees.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
