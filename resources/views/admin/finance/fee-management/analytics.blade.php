@extends('layouts.admin.layout')

@section('title', 'Fee Analytics & Reports')

@section('content')

<div class="max-w-6xl mx-auto px-4 py-6">

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Analytics & Reports</h1>
        <p class="text-gray-600">Detailed fee collection analysis and insights</p>
    </div>

    <!-- Navigation Tabs -->
    <div class="mb-6 border-b border-gray-200">
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('finance.fee-management.index') }}" class="px-4 py-3 border-b-2 border-transparent text-gray-600 hover:text-blue-600 hover:border-blue-600">Dashboard</a>
            <a href="{{ route('finance.fee-management.categories') }}" class="px-4 py-3 border-b-2 border-transparent text-gray-600 hover:text-blue-600 hover:border-blue-600">Categories</a>
            <a href="{{ route('finance.fee-management.structures') }}" class="px-4 py-3 border-b-2 border-transparent text-gray-600 hover:text-blue-600 hover:border-blue-600">Structures</a>
            <a href="{{ route('finance.fee-management.special-fees') }}" class="px-4 py-3 border-b-2 border-transparent text-gray-600 hover:text-blue-600 hover:border-blue-600">Special Fees</a>
            <a href="{{ route('finance.fee-management.payments') }}" class="px-4 py-3 border-b-2 border-transparent text-gray-600 hover:text-blue-600 hover:border-blue-600">Payments</a>
            <a href="{{ route('finance.fee-management.analytics') }}" class="px-4 py-3 border-b-2 border-blue-600 text-blue-600 font-semibold">Analytics</a>
        </div>
    </div>

    <!-- Payment Status Summary -->
    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Payment Status Summary</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach ($paymentStatusBreakdown as $status)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <p class="text-gray-600 text-sm font-semibold mb-2">{{ ucfirst($status->payment_status) }}</p>
                    <p class="text-3xl font-bold text-gray-800 mb-2">{{ $status->count }}</p>
                    <p class="text-lg font-semibold text-blue-600">₹{{ number_format($status->total, 2) }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Category-wise Breakdown -->
    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Category-wise Fee Breakdown</h2>
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Category</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">Count</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">Total Amount</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">Average Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categoryBreakdown as $category)
                            <tr class="border-t hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-semibold text-gray-800">{{ $category->name }}</td>
                                <td class="px-6 py-4 text-right text-sm text-gray-700">{{ $category->structureItems->count() }}</td>
                                <td class="px-6 py-4 text-right text-sm font-semibold text-gray-800">₹{{ number_format($category->structureItems->sum('amount'), 2) }}</td>
                                <td class="px-6 py-4 text-right text-sm text-gray-700">
                                    @if ($category->structureItems->count() > 0)
                                        ₹{{ number_format($category->structureItems->avg('amount'), 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">No categories found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Export Options -->
    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Export Reports</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('finance.fee-management.export') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 px-6 rounded-lg text-center transition">
                📥 Export All Fees (CSV)
            </a>
            <a href="#" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-4 px-6 rounded-lg text-center transition">
                📊 Export to Excel
            </a>
            <a href="#" class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-4 px-6 rounded-lg text-center transition">
                📄 Generate PDF Report
            </a>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-md p-6 text-white">
            <p class="text-blue-100 text-sm font-semibold mb-2">Total Invoices</p>
            <p class="text-3xl font-bold">{{ $paymentStatusBreakdown->sum('count') }}</p>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-md p-6 text-white">
            <p class="text-green-100 text-sm font-semibold mb-2">Paid Invoices</p>
            <p class="text-3xl font-bold">
                @php
                    $paid = $paymentStatusBreakdown->firstWhere('payment_status', 'paid');
                @endphp
                {{ $paid?->count ?? 0 }}
            </p>
        </div>
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-md p-6 text-white">
            <p class="text-orange-100 text-sm font-semibold mb-2">Pending Invoices</p>
            <p class="text-3xl font-bold">
                @php
                    $pending = $paymentStatusBreakdown->firstWhere('payment_status', 'pending');
                @endphp
                {{ $pending?->count ?? 0 }}
            </p>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-md p-6 text-white">
            <p class="text-purple-100 text-sm font-semibold mb-2">Collection Rate</p>
            <p class="text-3xl font-bold">
                @php
                    $total = $paymentStatusBreakdown->sum('count');
                    $paid = $paymentStatusBreakdown->firstWhere('payment_status', 'paid');
                    $rate = $total > 0 ? round(($paid?->count ?? 0) / $total * 100) : 0;
                @endphp
                {{ $rate }}%
            </p>
        </div>
    </div>

</div>

@endsection
