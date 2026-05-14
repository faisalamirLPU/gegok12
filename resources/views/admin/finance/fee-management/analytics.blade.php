@extends('layouts.admin.layout')

@section('title', 'Fee Analytics')

@php
    $academicYear = \App\Helpers\SiteHelper::getAcademicYear(auth()->user()->school_id);
@endphp

@section('content')
    <div class="relative">
        {{-- Page Header --}}
        <div class="flex flex-wrap lg:flex-row justify-between my-3">
            <div>
                <h1 class="admin-h1 my-3">Analytics & Reports</h1>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('finance.fee-management.export') }}"
                    class="no-underline text-white px-4 my-3 mx-1 flex items-center custom-green py-1 justify-center">
                    <span class="mx-1 text-sm font-semibold">Export CSV</span>
                    <svg class="w-3 h-3 fill-current text-white ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                </a>
            </div>
        </div>

        {{-- Navigation --}}
        @include('admin.finance.partials.tabs')

        {{-- Overview Cards --}}
        <div class="flex flex-wrap -mx-1 mt-4">
            @php
                $paidStat = $paymentStatusBreakdown->firstWhere('payment_status', 'paid');
                $partialStat = $paymentStatusBreakdown->firstWhere('payment_status', 'partial');
                $pendingStat = $paymentStatusBreakdown->firstWhere('payment_status', 'pending');
                $advanceStat = $paymentStatusBreakdown->firstWhere('payment_status', 'advance');
            @endphp
            <div class="w-full lg:w-1/4 md:w-1/2 px-1 my-2">
                <div class="bg-white custom-shadow px-4 py-3 border">
                    <p class="text-xs font-medium text-gray-500">Total Demand</p>
                    <p class="mt-1 text-xl font-bold text-gray-800">Rs. {{ number_format($totalDemand ?? 0, 0) }}</p>
                </div>
            </div>
            <div class="w-full lg:w-1/4 md:w-1/2 px-1 my-2">
                <div class="bg-white custom-shadow px-4 py-3 border border-green-100">
                    <p class="text-xs font-medium text-green-600">Total Collected</p>
                    <p class="mt-1 text-xl font-bold text-green-700">Rs. {{ number_format($totalCollected ?? 0, 0) }}</p>
                </div>
            </div>
            <div class="w-full lg:w-1/4 md:w-1/2 px-1 my-2">
                <div class="bg-white custom-shadow px-4 py-3 border border-red-100">
                    <p class="text-xs font-medium text-red-600">Total Pending</p>
                    <p class="mt-1 text-xl font-bold text-red-700">Rs. {{ number_format($totalPending ?? 0, 0) }}</p>
                </div>
            </div>
            <div class="w-full lg:w-1/4 md:w-1/2 px-1 my-2">
                <div class="bg-white custom-shadow px-4 py-3 border border-blue-100">
                    <p class="text-xs font-medium text-blue-600">Collection Rate</p>
                    <p class="mt-1 text-xl font-bold text-blue-700">{{ $collectionRate ?? 0 }}%</p>
                </div>
            </div>
        </div>

        {{-- Charts Row --}}
        <div class="flex flex-wrap -mx-1 mt-4">
            {{-- Monthly Collection Bar Chart --}}
            <div class="w-full lg:w-1/2 px-1 my-2">
                <div class="bg-white custom-shadow p-4 border h-full">
                    <h3 class="text-sm font-semibold text-gray-800 mb-3 border-b pb-2">Monthly Collection Trend</h3>
                    <div class="h-64">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Payment Status Doughnut --}}
            <div class="w-full lg:w-1/2 px-1 my-2">
                <div class="bg-white custom-shadow p-4 border h-full">
                    <h3 class="text-sm font-semibold text-gray-800 mb-3 border-b pb-2">Payment Status Breakdown</h3>
                    <div class="flex items-center justify-center h-48">
                        <canvas id="statusChart"></canvas>
                    </div>
                    <div class="flex flex-wrap justify-center gap-4 mt-3">
                        <span class="flex items-center gap-1.5 text-xs"><span
                                class="w-2.5 h-2.5 rounded-full bg-green-500"></span> Paid
                            ({{ $paidStat->count ?? 0 }})</span>
                        <span class="flex items-center gap-1.5 text-xs"><span
                                class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Partial
                            ({{ $partialStat->count ?? 0 }})</span>
                        <span class="flex items-center gap-1.5 text-xs"><span
                                class="w-2.5 h-2.5 rounded-full bg-red-500"></span> Pending
                            ({{ $pendingStat->count ?? 0 }})</span>
                        <span class="flex items-center gap-1.5 text-xs"><span
                                class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Advance
                            ({{ $advanceStat->count ?? 0 }})</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Category Breakdown --}}
        <div class="mt-6 bg-white custom-shadow border overflow-hidden">
            <div class="px-4 py-3 border-b">
                <h3 class="text-sm font-semibold text-gray-800">Category-wise Fee Breakdown</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">
                                Category</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wide">
                                Type</th>
                            <th
                                class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wide hidden sm:table-cell">
                                Total Amount</th>
                            <th
                                class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wide hidden md:table-cell">
                                Items Count</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($categoryBreakdown as $category)
                            @php
                                $total = $category->analytics_total_amount ?? $category->structureItems->sum('amount');
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="w-2 h-2 rounded-full {{ $category->status ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                                        <div>
                                            <div class="font-medium text-gray-900 text-xs">{{ $category->name }}</div>
                                            @if($category->code)
                                                <div class="text-xs text-gray-400">{{ $category->code }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($category->is_optional)
                                        <span
                                            class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-purple-100 text-purple-700">Optional</span>
                                    @else
                                        <span
                                            class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-600">Mandatory</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-900 text-xs hidden sm:table-cell">
                                    Rs. {{ number_format($total, 0) }}
                                </td>
                                <td class="px-4 py-3 text-right text-gray-600 text-xs hidden md:table-cell">
                                    {{ $category->structureItems->count() }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-400 text-xs">No category data available
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Due Date Overdue --}}
        @php
            $overdueCount = $categoryBreakdown->filter(function ($cat) {
                return $cat->feeItems->count() > 0; })->count();
            $totalCategoryAmount = $categoryBreakdown->sum(fn($c) => $c->feeItems->sum('amount'));
        @endphp
        <div class="flex flex-wrap -mx-1 mt-4">
            <div class="w-full lg:w-1/4 md:w-1/2 px-1 my-2">
                <div class="bg-white custom-shadow px-4 py-3 border text-center">
                    <p class="text-xs font-medium text-gray-500">Paid Invoices</p>
                    <p class="mt-1 text-xl font-bold text-green-600">{{ $paidStat->count ?? 0 }}</p>
                </div>
            </div>
            <div class="w-full lg:w-1/4 md:w-1/2 px-1 my-2">
                <div class="bg-white custom-shadow px-4 py-3 border text-center">
                    <p class="text-xs font-medium text-gray-500">Pending Invoices</p>
                    <p class="mt-1 text-xl font-bold text-red-600">{{ $pendingStat->count ?? 0 }}</p>
                </div>
            </div>
            <div class="w-full lg:w-1/4 md:w-1/2 px-1 my-2">
                <div class="bg-white custom-shadow px-4 py-3 border text-center">
                    <p class="text-xs font-medium text-gray-500">Total Categories</p>
                    <p class="mt-1 text-xl font-bold text-gray-800">{{ $categoryBreakdown->count() }}</p>
                </div>
            </div>
            <div class="w-full lg:w-1/4 md:w-1/2 px-1 my-2">
                <div class="bg-white custom-shadow px-4 py-3 border text-center">
                    <p class="text-xs font-medium text-gray-500">Total Demand</p>
                    <p class="mt-1 text-xl font-bold text-blue-600">Rs. {{ number_format($totalDemand, 0) }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.min.js"></script>
    <script>
        (function () {
            // Monthly Collection Bar Chart
            var monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
            var monthLabels = {!! json_encode($monthlyCollection->pluck('month')->map(fn($m) => date('M', mktime(0, 0, 0, $m, 1)))->values()->toArray()) !!};
            var monthData = {!! json_encode($monthlyCollection->pluck('total')->values()->toArray()) !!};

            new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: monthLabels,
                    datasets: [{
                        label: 'Collection',
                        data: monthData,
                        backgroundColor: '#3b82f6',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (ctx) {
                                    return 'Rs. ' + ctx.raw.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (val) {
                                    return 'Rs. ' + val.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });

            // Status Doughnut Chart
            var statusCtx = document.getElementById('statusChart').getContext('2d');
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Paid', 'Partial', 'Pending', 'Advance'],
                    datasets: [{
                        data: [
                        {{ $paidStat->count ?? 0 }},
                        {{ $partialStat->count ?? 0 }},
                        {{ $pendingStat->count ?? 0 }},
                            {{ $advanceStat->count ?? 0 }}
                        ],
                        backgroundColor: ['#22c55e', '#f59e0b', '#ef4444', '#3b82f6'],
                        borderWidth: 0
                    }]
                },
                options: {
                    cutout: '65%',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (tooltipItem) {
                                    return tooltipItem.label + ': ' + tooltipItem.raw + ' invoices';
                                }
                            }
                        }
                    }
                }
            });
        })();
    </script>
@endpush