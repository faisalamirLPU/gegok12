@extends('layouts.admin.layout')

@use('Illuminate\Support\Str')

@section('title', 'Fee Management')

@section('content')
<div class="relative">
    {{-- Page Header --}}
    <div class="flex flex-wrap lg:flex-row justify-between my-3">
        <div>
            <h1 class="admin-h1 my-3">Fee Management ({{ $academicYear->name ?? 'N/A' }})</h1>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                {{ $stats['paid_invoices'] }} Paid
            </span>
            <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full">
                {{ $stats['pending_payments'] }} Pending
            </span>
        </div>
    </div>

    {{-- Navigation --}}
    @include('admin.finance.partials.tabs')

    {{-- Stats Row --}}
    <div class="flex flex-wrap my-2">
        <div class="w-full lg:w-1/6 md:w-1/3 px-1 my-2">
            <div class="bg-white custom-shadow px-4 py-3 border">
                <div class="text-center">
                    <p class="text-gray-500 text-xs">Total Collected</p>
                    <h2 class="text-xl font-bold text-green-600 mt-1">Rs. {{ number_format($stats['total_collection'], 0) }}</h2>
                </div>
            </div>
        </div>
        <div class="w-full lg:w-1/6 md:w-1/3 px-1 my-2">
            <div class="bg-white custom-shadow px-4 py-3 border">
                <div class="text-center">
                    <p class="text-gray-500 text-xs">Pending</p>
                    <h2 class="text-xl font-bold text-red-600 mt-1">Rs. {{ number_format($stats['pending_collection'], 0) }}</h2>
                </div>
            </div>
        </div>
        <div class="w-full lg:w-1/6 md:w-1/3 px-1 my-2">
            <div class="bg-white custom-shadow px-4 py-3 border">
                <div class="text-center">
                    <p class="text-gray-500 text-xs">Advance</p>
                    <h2 class="text-xl font-bold text-blue-600 mt-1">Rs. {{ number_format($stats['advance_collection'], 0) }}</h2>
                </div>
            </div>
        </div>
        <div class="w-full lg:w-1/6 md:w-1/3 px-1 my-2">
            <div class="bg-white custom-shadow px-4 py-3 border">
                <div class="text-center">
                    <p class="text-gray-500 text-xs">Invoices</p>
                    <h2 class="text-xl font-bold text-gray-800 mt-1">{{ number_format($stats['total_invoices']) }}</h2>
                </div>
            </div>
        </div>
        <div class="w-full lg:w-1/6 md:w-1/3 px-1 my-2">
            <div class="bg-white custom-shadow px-4 py-3 border">
                <div class="text-center">
                    <p class="text-gray-500 text-xs">Partial</p>
                    <h2 class="text-xl font-bold text-yellow-600 mt-1">{{ number_format($stats['partial_payments']) }}</h2>
                </div>
            </div>
        </div>
        <div class="w-full lg:w-1/6 md:w-1/3 px-1 my-2">
            <div class="bg-white custom-shadow px-4 py-3 border">
                <div class="text-center">
                    <p class="text-gray-500 text-xs">Categories</p>
                    <h2 class="text-xl font-bold text-gray-800 mt-1">{{ $stats['active_categories'] }}/{{ $stats['total_categories'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="flex flex-wrap -mx-1 mt-4">
        {{-- Collection by Status (Doughnut) --}}
        <div class="w-full lg:w-1/3 px-1 my-2">
            <div class="bg-white custom-shadow p-4 border h-full">
                <h3 class="text-sm font-semibold text-gray-800 mb-3 border-b pb-2">Payment Status Distribution</h3>
                <div class="flex items-center justify-center">
                    <canvas id="statusChart" class="max-h-48"></canvas>
                </div>
                @php
                    $totalInvoiceVal = $stats['total_invoices'] > 0 ? $stats['total_invoices'] : 1;
                    $paidPct = round($stats['paid_invoices'] / $totalInvoiceVal * 100);
                    $partialPct = round($stats['partial_payments'] / $totalInvoiceVal * 100);
                    $pendingPct = 100 - $paidPct - $partialPct;
                @endphp
                <div class="flex flex-wrap justify-center gap-3 mt-3">
                    <span class="flex items-center gap-1 text-xs"><span class="w-2.5 h-2.5 rounded-full bg-green-500"></span> Paid {{ $paidPct }}%</span>
                    <span class="flex items-center gap-1 text-xs"><span class="w-2.5 h-2.5 rounded-full bg-yellow-500"></span> Partial {{ $partialPct }}%</span>
                    <span class="flex items-center gap-1 text-xs"><span class="w-2.5 h-2.5 rounded-full bg-red-500"></span> Pending {{ $pendingPct }}%</span>
                </div>
            </div>
        </div>

        {{-- Collection vs Pending (Horizontal Bar) --}}
        <div class="w-full lg:w-1/3 px-1 my-2">
            <div class="bg-white custom-shadow p-4 border h-full">
                <h3 class="text-sm font-semibold text-gray-800 mb-3 border-b pb-2">Collection Overview</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-xs text-gray-600 mb-1">
                            <span>Collected</span>
                            <span class="font-semibold text-green-600">Rs. {{ number_format($stats['total_collection'], 0) }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5">
                            @php $totalDemand = $stats['total_collection'] + $stats['pending_collection']; @endphp
                            <div class="bg-green-500 h-2.5 rounded-full" style="width: {{ $totalDemand > 0 ? round($stats['total_collection'] / $totalDemand * 100) : 0 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs text-gray-600 mb-1">
                            <span>Pending</span>
                            <span class="font-semibold text-red-600">Rs. {{ number_format($stats['pending_collection'], 0) }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5">
                            <div class="bg-red-500 h-2.5 rounded-full" style="width: {{ $totalDemand > 0 ? round($stats['pending_collection'] / $totalDemand * 100) : 0 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-xs text-gray-600 mb-1">
                            <span>Advance</span>
                            <span class="font-semibold text-blue-600">Rs. {{ number_format($stats['advance_collection'], 0) }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5">
                            <div class="bg-blue-500 h-2.5 rounded-full" style="width: {{ $totalDemand > 0 ? min(100, round($stats['advance_collection'] / max($totalDemand, 1) * 100)) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Stats Summary --}}
        <div class="w-full lg:w-1/3 px-1 my-2">
            <div class="bg-white custom-shadow p-4 border h-full">
                <h3 class="text-sm font-semibold text-gray-800 mb-3 border-b pb-2">Quick Access</h3>
                <div class="space-y-2.5">
                    <a href="{{ route('finance.fee-management.payments', ['status' => 'pending']) }}" class="flex items-center justify-between p-2.5 rounded bg-red-50 hover:bg-red-100 transition border border-red-100">
                        <div class="flex items-center gap-2.5">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-red-200 text-red-700 font-bold text-sm">{{ $stats['pending_payments'] }}</span>
                            <div>
                                <p class="text-xs font-semibold text-gray-800">Pending Payments</p>
                                <p class="text-xs text-gray-500">Awaiting collection</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('finance.fee-management.special-fees') }}" class="flex items-center justify-between p-2.5 rounded bg-purple-50 hover:bg-purple-100 transition border border-purple-100">
                        <div class="flex items-center gap-2.5">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-purple-200 text-purple-700 font-semibold text-sm">{{ $stats['total_special_fees'] }}</span>
                            <div>
                                <p class="text-xs font-semibold text-gray-800">Special Fees</p>
                                <p class="text-xs text-gray-500">One-time fees</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('finance.fee-management.structures') }}" class="flex items-center justify-between p-2.5 rounded bg-blue-50 hover:bg-blue-100 transition border border-blue-100">
                        <div class="flex items-center gap-2.5">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-200 text-blue-700 font-semibold text-sm">{{ $stats['total_structures'] }}</span>
                            <div>
                                <p class="text-xs font-semibold text-gray-800">Fee Structures</p>
                                <p class="text-xs text-gray-500">Class-wise structures</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Invoices & Special Fees --}}
    <div class="flex flex-wrap -mx-1 mt-4">
        {{-- Recent Invoices --}}
        <div class="w-full lg:w-1/2 px-1 my-2">
            <div class="bg-white custom-shadow border overflow-hidden">
                <div class="px-4 py-3 border-b flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-800">Recent Invoices</h3>
                    <a href="{{ route('finance.fee-management.payments') }}" class="text-xs text-blue-600 hover:underline">View all</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b">
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Invoice</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Student</th>
                                <th class="px-3 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Amount</th>
                                <th class="px-3 py-2 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($recentFees as $fee)
                                @php
                                    $profile = optional($fee->student)->userprofile;
                                    $studentName = trim(($profile->firstname ?? '') . ' ' . ($profile->lastname ?? '')) ?: ($fee->student->name ?? 'N/A');
                                    $statusConfig = [
                                        'paid' => ['bg-green-100', 'text-green-700'],
                                        'partial' => ['bg-yellow-100', 'text-yellow-700'],
                                        'pending' => ['bg-red-100', 'text-red-700'],
                                        'advance' => ['bg-blue-100', 'text-blue-700'],
                                    ];
                                    $status = strtolower($fee->erp_status ?? 'pending');
                                    $s = $statusConfig[$status] ?? ['bg-gray-100', 'text-gray-700'];
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2">
                                        <div class="font-semibold text-blue-700 text-xs">{{ $fee->invoice_no }}</div>
                                        <div class="text-xs text-gray-400">{{ $fee->billing_cycle ?? $fee->payment_period ?? '—' }}</div>
                                    </td>
                                    <td class="px-3 py-2 text-gray-900 text-xs">{{ Str::limit($studentName, 18, '..') }}</td>
                                    <td class="px-3 py-2 text-right font-semibold text-gray-900 text-xs">Rs. {{ number_format($fee->total_amount, 0) }}</td>
                                    <td class="px-3 py-2 text-center">
                                        <span class="inline-flex px-1.5 py-0.5 rounded-full text-xs font-semibold {{ $s[0] }} {{ $s[1] }}">
                                            {{ ucfirst($status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-3 py-6 text-center text-gray-400 text-xs">No recent invoices</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Recent Special Fees --}}
        <div class="w-full lg:w-1/2 px-1 my-2">
            <div class="bg-white custom-shadow border overflow-hidden">
                <div class="px-4 py-3 border-b flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-800">Recent Special Fees</h3>
                    <a href="{{ route('finance.fee-management.special-fees') }}" class="text-xs text-blue-600 hover:underline">View all</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 border-b">
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Student</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Category</th>
                                <th class="px-3 py-2 text-right text-xs font-semibold text-gray-500 uppercase">Amount</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">Due Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($recentSpecialFees as $fee)
                                @php
                                    $profile = optional($fee->student)->userprofile;
                                    $studentName = trim(($profile->firstname ?? '') . ' ' . ($profile->lastname ?? '')) ?: ($fee->student->name ?? 'N/A');
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2 text-gray-900 text-xs">{{ Str::limit($studentName, 16, '..') }}</td>
                                    <td class="px-3 py-2 text-gray-700 text-xs">{{ $fee->feeCategory->name ?? '—' }}</td>
                                    <td class="px-3 py-2 text-right font-semibold text-gray-900 text-xs">Rs. {{ number_format($fee->amount, 0) }}</td>
                                    <td class="px-3 py-2 text-gray-600 text-xs">
                                        @if($fee->due_date)
                                            {{ $fee->due_date->format('d M') }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-3 py-6 text-center text-gray-400 text-xs">No special fees</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.min.js"></script>
<script>
(function() {
    // Status Doughnut Chart
    var ctx = document.getElementById('statusChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Paid', 'Partial', 'Pending'],
            datasets: [{
                data: [
                    {{ $stats['paid_invoices'] }},
                    {{ $stats['partial_payments'] }},
                    {{ max(0, $stats['pending_payments']) }}
                ],
                backgroundColor: ['#22c55e', '#eab308', '#ef4444'],
                borderWidth: 0
            }]
        },
        options: {
            cutout: '65%',
            legend: { display: false },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem) {
                        return tooltipItem.label + ': ' + tooltipItem.raw;
                    }
                }
            }
        }
    });
})();
</script>
@endpush
