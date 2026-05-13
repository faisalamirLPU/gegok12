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
    <div class="bg-white custom-shadow border p-4 mb-4 mt-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Month</label>
                <select name="month" class="rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-0">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" @selected($month == $m)>
                            {{ Carbon\Carbon::createFromDate(null, $m, 1)->format('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Year</label>
                <select name="year" class="rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-0">
                    @foreach(range(now()->year - 1, now()->year + 1) as $y)
                        <option value="{{ $y }}" @selected($year == $y)>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="no-underline text-white px-6 flex items-center custom-green py-1.5 justify-center">
                <span class="text-sm font-semibold">Apply Filter</span>
            </button>
        </form>
    </div>

    {{-- Yearly Overview --}}
    <h2 class="text-lg font-bold text-gray-800 mb-2 mt-4">Academic Year Overview</h2>
    <div class="flex flex-wrap -mx-1">
        <div class="w-full lg:w-1/6 md:w-1/3 px-1 my-2">
            <div class="bg-white custom-shadow p-4 border h-full">
                <p class="text-xs font-medium text-gray-500">Total Demand</p>
                <p class="text-xl font-bold text-gray-800">Rs. {{ number_format($yearlyStats['total_demand'], 0) }}</p>
            </div>
        </div>
        <div class="w-full lg:w-1/6 md:w-1/3 px-1 my-2">
            <div class="bg-white custom-shadow p-4 border border-green-100 h-full">
                <p class="text-xs font-medium text-green-600">Total Collected</p>
                <p class="text-xl font-bold text-green-700">Rs. {{ number_format($yearlyStats['total_collected'], 0) }}</p>
            </div>
        </div>
        <div class="w-full lg:w-1/6 md:w-1/3 px-1 my-2">
            <div class="bg-white custom-shadow p-4 border border-red-100 h-full">
                <p class="text-xs font-medium text-red-600">Total Pending</p>
                <p class="text-xl font-bold text-red-700">Rs. {{ number_format($yearlyStats['total_pending'], 0) }}</p>
            </div>
        </div>
        <div class="w-full lg:w-1/6 md:w-1/3 px-1 my-2">
            <div class="bg-white custom-shadow p-4 border border-blue-100 h-full">
                <p class="text-xs font-medium text-blue-600">Advance Amount</p>
                <p class="text-xl font-bold text-blue-700">Rs. {{ number_format($yearlyStats['advance_amount'], 0) }}</p>
            </div>
        </div>
        <div class="w-full lg:w-1/6 md:w-1/3 px-1 my-2">
            <div class="bg-white custom-shadow p-4 border h-full">
                <p class="text-xs font-medium text-gray-500">Paid Invoices</p>
                <p class="text-xl font-bold text-gray-800">{{ $yearlyStats['paid_invoices'] }}/{{ $yearlyStats['total_invoices'] }}</p>
            </div>
        </div>
        <div class="w-full lg:w-1/6 md:w-1/3 px-1 my-2">
            <div class="bg-white custom-shadow p-4 border h-full">
                <p class="text-xs font-medium text-gray-500">Collection Rate</p>
                <p class="text-xl font-bold text-blue-600">
                    {{ $yearlyStats['total_demand'] > 0 ? round(($yearlyStats['total_collected'] / $yearlyStats['total_demand']) * 100, 1) : 0 }}%
                </p>
            </div>
        </div>
    </div>

    {{-- Monthly Overview --}}
    <h2 class="text-lg font-bold text-gray-800 mb-2 mt-4">Monthly Performance - {{ Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}</h2>
    <div class="flex flex-wrap -mx-1">
        <div class="w-full lg:w-1/4 md:w-1/2 px-1 my-2">
            <div class="bg-white custom-shadow p-4 border h-full">
                <p class="text-xs font-medium text-gray-500">Monthly Demand</p>
                <p class="text-xl font-bold text-gray-800">Rs. {{ number_format($monthlyStats['total_demand'], 0) }}</p>
            </div>
        </div>
        <div class="w-full lg:w-1/4 md:w-1/2 px-1 my-2">
            <div class="bg-white custom-shadow p-4 border border-green-100 h-full">
                <p class="text-xs font-medium text-green-600">Collected</p>
                <p class="text-xl font-bold text-green-700">Rs. {{ number_format($monthlyStats['total_collected'], 0) }}</p>
            </div>
        </div>
        <div class="w-full lg:w-1/4 md:w-1/2 px-1 my-2">
            <div class="bg-white custom-shadow p-4 border border-red-100 h-full">
                <p class="text-xs font-medium text-red-600">Pending</p>
                <p class="text-xl font-bold text-red-700">Rs. {{ number_format($monthlyStats['total_pending'], 0) }}</p>
            </div>
        </div>
        <div class="w-full lg:w-1/4 md:w-1/2 px-1 my-2">
            <div class="bg-white custom-shadow p-4 border border-blue-100 h-full">
                <div class="flex justify-between items-center">
                    <p class="text-xs font-medium text-blue-600">Collection Rate</p>
                    <span class="text-xs text-gray-400">{{ $monthlyStats['paid_count'] }}/{{ $monthlyStats['invoice_count'] }} Inv</span>
                </div>
                <p class="text-xl font-bold text-blue-700">{{ $monthlyStats['collection_rate'] }}%</p>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap -mx-1 mt-4">
        {{-- Monthly Collection Chart --}}
        <div class="w-full lg:w-1/2 px-1 my-2">
            <div class="bg-white custom-shadow p-5 border h-full">
                <h3 class="text-sm font-semibold text-gray-800 mb-4 border-b pb-2">Monthly Collection Trend</h3>
                <div class="space-y-4">
                    @forelse($monthlyCollection as $item)
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-medium text-gray-700">{{ $item['month'] }}</span>
                                <span class="text-gray-400">Rs. {{ number_format($item['collected'], 0) }} / {{ number_format($item['demand'], 0) }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full transition-all" style="width: {{ $item['rate'] }}%"></div>
                            </div>
                            <div class="text-[10px] text-gray-400 text-right mt-1">{{ $item['rate'] }}% collected</div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4 text-sm">No collection data available</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Collection Distribution --}}
        <div class="w-full lg:w-1/2 px-1 my-2">
            <div class="bg-white custom-shadow p-5 border h-full">
                <h3 class="text-sm font-semibold text-gray-800 mb-4 border-b pb-2">Collection Summary</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-green-50/50 border border-green-100 rounded">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            <span class="text-xs font-medium text-gray-700">Collected</span>
                        </div>
                        <span class="text-xs font-bold text-green-700">Rs. {{ number_format($yearlyStats['total_collected'], 0) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-red-50/50 border border-red-100 rounded">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                            <span class="text-xs font-medium text-gray-700">Pending</span>
                        </div>
                        <span class="text-xs font-bold text-red-700">Rs. {{ number_format($yearlyStats['total_pending'], 0) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-blue-50/50 border border-blue-100 rounded">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                            <span class="text-xs font-medium text-gray-700">Advance</span>
                        </div>
                        <span class="text-xs font-bold text-blue-700">Rs. {{ number_format($yearlyStats['advance_amount'], 0) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 border border-gray-100 rounded">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 bg-gray-500 rounded-full"></div>
                            <span class="text-xs font-medium text-gray-700">Total Demand</span>
                        </div>
                        <span class="text-xs font-bold text-gray-800">Rs. {{ number_format($yearlyStats['total_demand'], 0) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap -mx-1 mt-4">
        {{-- Defaulters List --}}
        <div class="w-full lg:w-1/2 px-1 my-2">
            <div class="bg-white custom-shadow border overflow-hidden">
                <div class="px-5 py-3 border-b flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wider">Top Defaulters</h3>
                    <a href="{{ route('finance.fee-records.index', ['status' => 'pending']) }}" class="text-xs text-blue-600 hover:underline">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Student</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Class</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Balance</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Days</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($defaulters as $defaulter)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-xs">
                                        <a href="{{ route('finance.fee-records.show', $defaulter['student_id']) }}" class="font-medium text-gray-900 hover:text-blue-600 transition">
                                            {{ $defaulter['student_name'] }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-gray-600">{{ trim($defaulter['class'] . ' ' . $defaulter['section']) }}</td>
                                    <td class="px-4 py-3 text-xs text-right font-semibold text-red-600">Rs. {{ number_format($defaulter['balance'], 0) }}</td>
                                    <td class="px-4 py-3 text-xs text-right text-orange-600">
                                        @if($defaulter['days_overdue'] > 0)
                                            {{ $defaulter['days_overdue'] }}d
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-400 text-xs">No defaulters found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Top Paying Students --}}
        <div class="w-full lg:w-1/2 px-1 my-2">
            <div class="bg-white custom-shadow border overflow-hidden">
                <div class="px-5 py-3 border-b">
                    <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wider">Top Payers</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 w-16">Rank</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Student</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Total Paid</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($topPayingStudents as $student)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-[10px] font-bold
                                            @if($student['rank'] == 1) bg-yellow-100 text-yellow-700
                                            @elseif($student['rank'] == 2) bg-gray-200 text-gray-700
                                            @elseif($student['rank'] == 3) bg-orange-100 text-orange-700
                                            @else bg-gray-100 text-gray-600 @endif">
                                            {{ $student['rank'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $student['student_name'] }}</td>
                                    <td class="px-4 py-3 text-xs text-right font-semibold text-green-700">Rs. {{ number_format($student['total_paid'], 0) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-gray-400 text-xs">No payment data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Special Fee Stats --}}
    <h2 class="text-lg font-bold text-gray-800 mb-2 mt-4">Special Fee Analysis</h2>
    <div class="flex flex-wrap -mx-1">
        <div class="w-full lg:w-1/4 md:w-1/2 px-1 my-2">
            <div class="bg-white custom-shadow p-4 border h-full">
                <p class="text-xs font-medium text-gray-500">Special Fees Assigned</p>
                <p class="text-xl font-bold text-gray-800">{{ $specialFeeStats['total_count'] }}</p>
            </div>
        </div>
        <div class="w-full lg:w-1/4 md:w-1/2 px-1 my-2">
            <div class="bg-white custom-shadow p-4 border border-green-100 h-full">
                <p class="text-xs font-medium text-green-600">Active Special Fees</p>
                <p class="text-xl font-bold text-green-700">{{ $specialFeeStats['active_count'] }}</p>
            </div>
        </div>
        <div class="w-full lg:w-1/4 md:w-1/2 px-1 my-2">
            <div class="bg-white custom-shadow p-4 border border-blue-100 h-full">
                <p class="text-xs font-medium text-blue-600">Applied to Invoices</p>
                <p class="text-xl font-bold text-blue-700">{{ $specialFeeStats['applied_count'] }}</p>
            </div>
        </div>
        <div class="w-full lg:w-1/4 md:w-1/2 px-1 my-2">
            <div class="bg-white custom-shadow p-4 border h-full">
                <p class="text-xs font-medium text-gray-500">Special Fees Amount</p>
                <p class="text-xl font-bold text-gray-800">Rs. {{ number_format($specialFeeStats['total_amount'], 0) }}</p>
            </div>
        </div>
    </div>

    {{-- Recent Transactions --}}
    <div class="mt-6 bg-white custom-shadow border overflow-hidden">
        <div class="px-5 py-3 border-b">
            <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wider">Recent Transactions</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Student</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Invoice</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Description</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentTransactions as $tx)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-xs text-gray-600">{{ $tx['date']->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-xs font-medium text-gray-900">{{ $tx['student_name'] }}</td>
                            <td class="px-4 py-3 text-xs font-mono text-blue-700">{{ $tx['invoice_no'] }}</td>
                            <td class="px-4 py-3 text-xs text-gray-600">{{ $tx['description'] }}</td>
                            <td class="px-4 py-3 text-xs text-right font-semibold text-green-700">Rs. {{ number_format($tx['amount'], 0) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-400 text-xs">No recent transactions</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection