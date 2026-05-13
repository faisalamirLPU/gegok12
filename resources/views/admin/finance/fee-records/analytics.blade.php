@extends('layouts.admin.layout')

@section('title', 'Fee Analytics')

@section('content')
<div class="relative">
    <div class="flex flex-wrap lg:flex-row justify-between my-3">
        <div>
            <h1 class="admin-h1 my-3">Fee Analytics</h1>
        </div>
    </div>

    @include('admin.finance.partials.tabs')

    {{-- Filters --}}
    <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                <select name="month" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" @selected($month == $m)>
                            {{ Carbon\Carbon::createFromDate(null, $m, 1)->format('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                <select name="year" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500">
                    @foreach(range(now()->year - 1, now()->year + 1) as $y)
                        <option value="{{ $y }}" @selected($year == $y)>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium">
                Apply Filter
            </button>
        </form>
    </div>

    {{-- Yearly Overview --}}
    <h2 class="text-xl font-bold text-gray-900 mb-4">Academic Year Overview</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Total Demand</p>
            <p class="text-2xl font-bold text-gray-900">Rs. {{ number_format($yearlyStats['total_demand'], 0) }}</p>
        </div>
        <div class="bg-green-50 rounded-lg border border-green-200 p-4">
            <p class="text-sm text-green-600">Total Collected</p>
            <p class="text-2xl font-bold text-green-700">Rs. {{ number_format($yearlyStats['total_collected'], 0) }}</p>
        </div>
        <div class="bg-red-50 rounded-lg border border-red-200 p-4">
            <p class="text-sm text-red-600">Total Pending</p>
            <p class="text-2xl font-bold text-red-700">Rs. {{ number_format($yearlyStats['total_pending'], 0) }}</p>
        </div>
        <div class="bg-blue-50 rounded-lg border border-blue-200 p-4">
            <p class="text-sm text-blue-600">Advance Amount</p>
            <p class="text-2xl font-bold text-blue-700">Rs. {{ number_format($yearlyStats['advance_amount'], 0) }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Paid Invoices</p>
            <p class="text-2xl font-bold text-gray-900">{{ $yearlyStats['paid_invoices'] }} / {{ $yearlyStats['total_invoices'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Collection Rate</p>
            <p class="text-2xl font-bold text-gray-900">
                {{ $yearlyStats['total_demand'] > 0 ? round(($yearlyStats['total_collected'] / $yearlyStats['total_demand']) * 100, 1) : 0 }}%
            </p>
        </div>
    </div>

    {{-- Monthly Overview --}}
    <h2 class="text-xl font-bold text-gray-900 mb-4">Monthly Performance - {{ Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Monthly Demand</p>
            <p class="text-2xl font-bold text-gray-900">Rs. {{ number_format($monthlyStats['total_demand'], 0) }}</p>
        </div>
        <div class="bg-green-50 rounded-lg border border-green-200 p-4">
            <p class="text-sm text-green-600">Collected</p>
            <p class="text-2xl font-bold text-green-700">Rs. {{ number_format($monthlyStats['total_collected'], 0) }}</p>
        </div>
        <div class="bg-red-50 rounded-lg border border-red-200 p-4">
            <p class="text-sm text-red-600">Pending</p>
            <p class="text-2xl font-bold text-red-700">Rs. {{ number_format($monthlyStats['total_pending'], 0) }}</p>
        </div>
        <div class="bg-blue-50 rounded-lg border border-blue-200 p-4">
            <p class="text-sm text-blue-600">Collection Rate</p>
            <p class="text-2xl font-bold text-blue-700">{{ $monthlyStats['collection_rate'] }}%</p>
            <p class="text-xs text-gray-500">Invoices: {{ $monthlyStats['paid_count'] }} / {{ $monthlyStats['invoice_count'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Monthly Collection Chart --}}
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Collection Trend</h3>
            <div class="space-y-3">
                @forelse($monthlyCollection as $item)
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1">
                            <span class="font-medium text-gray-700">{{ $item['month'] }}</span>
                            <span class="text-gray-500">Rs. {{ number_format($item['collected'], 0) }} / Rs. {{ number_format($item['demand'], 0) }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-green-500 h-3 rounded-full transition-all" style="width: {{ $item['rate'] }}%"></div>
                        </div>
                        <div class="text-xs text-gray-400 text-right">{{ $item['rate'] }}% collected</div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">No collection data available</p>
                @endforelse
            </div>
        </div>

        {{-- Collection Distribution --}}
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Collection Summary</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <span class="font-medium text-gray-900">Collected</span>
                    </div>
                    <span class="font-bold text-green-700">Rs. {{ number_format($yearlyStats['total_collected'], 0) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                        <span class="font-medium text-gray-900">Pending</span>
                    </div>
                    <span class="font-bold text-red-700">Rs. {{ number_format($yearlyStats['total_pending'], 0) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        <span class="font-medium text-gray-900">Advance</span>
                    </div>
                    <span class="font-bold text-blue-700">Rs. {{ number_format($yearlyStats['advance_amount'], 0) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 bg-gray-500 rounded-full"></div>
                        <span class="font-medium text-gray-900">Total Demand</span>
                    </div>
                    <span class="font-bold text-gray-900">Rs. {{ number_format($yearlyStats['total_demand'], 0) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Defaulters List --}}
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Top Defaulters</h3>
                <a href="{{ route('finance.fee-records.index', ['status' => 'pending']) }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Student</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Class</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600">Balance</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600">Days</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($defaulters as $defaulter)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-sm">
                                    <a href="{{ route('finance.fee-records.show', $defaulter['student_id']) }}" class="font-medium text-gray-900 hover:text-blue-700">
                                        {{ $defaulter['student_name'] }}
                                    </a>
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-700">{{ trim($defaulter['class'] . ' ' . $defaulter['section']) }}</td>
                                <td class="px-4 py-2 text-sm text-right font-semibold text-red-600">Rs. {{ number_format($defaulter['balance'], 0) }}</td>
                                <td class="px-4 py-2 text-sm text-right text-orange-600">
                                    @if($defaulter['days_overdue'] > 0)
                                        {{ $defaulter['days_overdue'] }}d
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-gray-500">No defaulters found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Top Paying Students --}}
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Top Payers</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">#</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Student</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600">Total Paid</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($topPayingStudents as $student)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-sm">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold
                                        @if($student['rank'] == 1) bg-yellow-100 text-yellow-700
                                        @elseif($student['rank'] == 2) bg-gray-200 text-gray-700
                                        @elseif($student['rank'] == 3) bg-orange-100 text-orange-700
                                        @else bg-gray-100 text-gray-600 @endif">
                                        {{ $student['rank'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ $student['student_name'] }}</td>
                                <td class="px-4 py-2 text-sm text-right font-semibold text-green-700">Rs. {{ number_format($student['total_paid'], 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-6 text-center text-gray-500">No payment data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Special Fee Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Special Fees Assigned</p>
            <p class="text-2xl font-bold text-gray-900">{{ $specialFeeStats['total_count'] }}</p>
        </div>
        <div class="bg-green-50 rounded-lg border border-green-200 p-4">
            <p class="text-sm text-green-600">Active Special Fees</p>
            <p class="text-2xl font-bold text-green-700">{{ $specialFeeStats['active_count'] }}</p>
        </div>
        <div class="bg-blue-50 rounded-lg border border-blue-200 p-4">
            <p class="text-sm text-blue-600">Applied to Invoices</p>
            <p class="text-2xl font-bold text-blue-700">{{ $specialFeeStats['applied_count'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Special Fees Amount</p>
            <p class="text-2xl font-bold text-gray-900">Rs. {{ number_format($specialFeeStats['total_amount'], 0) }}</p>
        </div>
    </div>

    {{-- Recent Transactions --}}
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Recent Transactions</h3>
        </div>
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Date</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Student</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Invoice</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Description</th>
                    <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600">Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($recentTransactions as $tx)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $tx['date']->format('d M Y') }}</td>
                        <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ $tx['student_name'] }}</td>
                        <td class="px-4 py-2 text-sm font-mono text-blue-700">{{ $tx['invoice_no'] }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $tx['description'] }}</td>
                        <td class="px-4 py-2 text-sm text-right font-semibold text-green-700">Rs. {{ number_format($tx['amount'], 0) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">No recent transactions</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection