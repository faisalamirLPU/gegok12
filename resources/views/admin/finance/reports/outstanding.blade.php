@extends('layouts.admin.layout')

@section('title', 'Outstanding Dues Report')

@section('content')
<div class="relative">

    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Finance Reports</h1>
            <p class="text-sm text-gray-500">Enterprise Financial Analytics and Reporting</p>
        </div>
    </div>

    @include('admin.finance.reports.partials.tabs')

    {{-- Filter Form --}}
    <div class="bg-white p-4 rounded border mb-6 shadow-sm">
        <form action="{{ route('finance.reports.outstanding') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Due Date From</label>
                <input type="date" name="due_date_from" value="{{ $filters['due_date_from'] ?? '' }}" class="w-full text-sm border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Due Date To</label>
                <input type="date" name="due_date_to" value="{{ $filters['due_date_to'] ?? '' }}" class="w-full text-sm border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm font-semibold hover:bg-blue-700">Filter</button>
                <a href="{{ route('finance.reports.outstanding') }}" class="ml-2 text-sm text-gray-500 hover:text-gray-800">Clear</a>
            </div>
        </form>
    </div>

    {{-- Outstanding Metrics --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded border shadow-sm p-4 border-l-4 border-red-500">
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Total Outstanding</p>
            <p class="text-2xl font-black text-gray-800 mt-1">Rs. {{ number_format($report['total_outstanding'], 0) }}</p>
        </div>
        
        <div class="bg-white rounded border shadow-sm p-4 border-l-4 border-orange-500">
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Overdue Invoices</p>
            <p class="text-xl font-bold text-gray-700 mt-1">{{ number_format($report['overdue_count'], 0) }}</p>
        </div>

        <div class="bg-white rounded border shadow-sm p-4 border-l-4 border-yellow-500">
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Partially Paid</p>
            <p class="text-xl font-bold text-gray-700 mt-1">{{ number_format($report['partial_count'], 0) }}</p>
        </div>

        <div class="bg-white rounded border shadow-sm p-4 border-l-4 border-gray-500">
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Fully Unpaid</p>
            <p class="text-xl font-bold text-gray-700 mt-1">{{ number_format($report['unpaid_count'], 0) }}</p>
        </div>
    </div>

    {{-- Dues List --}}
    <div class="bg-white rounded border shadow-sm overflow-hidden mb-8">
        <div class="px-4 py-3 bg-gray-50 border-b flex justify-between items-center">
            <h3 class="font-bold text-gray-700">Pending & Partial Invoices</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($report['records'] as $fee)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 font-medium">
                            <a href="{{ route('finance.fee-records.show', $fee->user_id) }}" target="_blank">
                                {{ $fee->invoice_no }}
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ optional($fee->student->userprofile)->firstname }} {{ optional($fee->student->userprofile)->lastname }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ optional($fee->studentAcademic->standardLink->standard)->name }} - {{ optional($fee->studentAcademic->standardLink->section)->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm {{ $fee->due_date && $fee->due_date < now() ? 'text-red-500 font-bold' : 'text-gray-500' }}">
                            {{ optional($fee->due_date)->format('d M Y') ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 text-right font-medium">
                            Rs. {{ number_format($fee->total_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 text-right font-bold">
                            Rs. {{ number_format($fee->balance, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $fee->status == 'partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800' }}">
                                {{ strtoupper($fee->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 whitespace-nowrap text-sm text-gray-500 text-center">No outstanding dues found for the selected criteria.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
