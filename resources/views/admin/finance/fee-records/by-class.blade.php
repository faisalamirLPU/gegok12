@extends('layouts.admin.layout')

@section('title', "Fee Records - {$classLink->standard->name ?? ''} {$classLink->section->name ?? ''}")

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
            <a href="{{ route('finance.fee-records.index') }}" class="hover:text-blue-600">Fee Records</a>
            <span>/</span>
            <span>{{ $classLink->standard->name ?? '' }} {{ $classLink->section->name ?? '' }}</span>
        </div>
        <h1 class="text-3xl font-bold text-gray-900">
            {{ $classLink->standard->name ?? '' }} {{ $classLink->section->name ?? '' }}
        </h1>
        <p class="text-gray-600 mt-1">{{ $paymentPeriod }} - {{ $academicYear->name }}</p>
    </div>

    @include('admin.finance.partials.navigation')

    {{-- Month Selector --}}
    <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <input type="hidden" name="class_id" value="{{ $classLink->id }}">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Month</label>
                <select name="month" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" @selected($month == $m)>
                            {{ Carbon\Carbon::createFromDate(null, $m, 1)->format('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                <select name="year" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @foreach(range(now()->year - 1, now()->year + 1) as $y)
                        <option value="{{ $y }}" @selected($year == $y)>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium">
                Filter
            </button>
        </form>
    </div>

    {{-- Class Summary --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Total Students</p>
            <p class="text-2xl font-bold text-gray-900">{{ $summary['total_students'] }}</p>
        </div>
        <div class="bg-green-50 rounded-lg border border-green-200 p-4">
            <p class="text-sm text-green-600">Paid</p>
            <p class="text-2xl font-bold text-green-700">{{ $summary['paid_count'] }}</p>
        </div>
        <div class="bg-yellow-50 rounded-lg border border-yellow-200 p-4">
            <p class="text-sm text-yellow-600">Partial</p>
            <p class="text-2xl font-bold text-yellow-700">{{ $summary['partial_count'] }}</p>
        </div>
        <div class="bg-red-50 rounded-lg border border-red-200 p-4">
            <p class="text-sm text-red-600">Pending</p>
            <p class="text-2xl font-bold text-red-700">{{ $summary['pending_count'] }}</p>
        </div>
        <div class="bg-blue-50 rounded-lg border border-blue-200 p-4">
            <p class="text-sm text-blue-600">No Invoice</p>
            <p class="text-2xl font-bold text-blue-700">{{ $summary['no_invoice_count'] }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Total Demand</p>
            <p class="text-2xl font-bold text-gray-900">Rs. {{ number_format($summary['total_demand'], 0) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-green-50 rounded-lg border border-green-200 p-4">
            <p class="text-sm text-green-600">Collected</p>
            <p class="text-2xl font-bold text-green-700">Rs. {{ number_format($summary['total_collected'], 0) }}</p>
        </div>
        <div class="bg-red-50 rounded-lg border border-red-200 p-4">
            <p class="text-sm text-red-600">Pending Amount</p>
            <p class="text-2xl font-bold text-red-700">Rs. {{ number_format($summary['total_pending'], 0) }}</p>
        </div>
        <div class="bg-blue-50 rounded-lg border border-blue-200 p-4">
            <p class="text-sm text-blue-600">Collection %</p>
            <p class="text-2xl font-bold text-blue-700">
                {{ $summary['total_demand'] > 0 ? round(($summary['total_collected'] / $summary['total_demand']) * 100) : 0 }}%
            </p>
        </div>
    </div>

    {{-- Student List --}}
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Student</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase text-gray-600">Invoice</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-600">Total</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-600">Paid</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-600">Balance</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-600">Advance</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase text-gray-600">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($studentRecords as $record)
                        @php
                            $student = $record['student'];
                            $fee = $record['fee'];
                            $profile = $student->userprofile;
                            $studentName = trim(($profile->firstname ?? '') . ' ' . ($profile->lastname ?? '')) ?: ($student->name ?? 'N/A');
                            $statusClass = $fee ? $fee->erp_status_badge_class : 'bg-gray-100 text-gray-700 border border-gray-200';
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <a href="{{ route('finance.fee-records.show', $student->id) }}" class="font-medium text-gray-900 hover:text-blue-700">
                                    {{ $studentName }}
                                </a>
                                <div class="text-xs text-gray-500">{{ $student->name }}</div>
                            </td>
                            <td class="px-4 py-3 text-center text-sm">
                                @if($fee)
                                    <span class="font-mono text-blue-700">{{ $fee->invoice_no }}</span>
                                @else
                                    <span class="text-gray-400">No Invoice</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-semibold">
                                Rs. {{ $fee ? number_format($fee->total_amount, 2) : '0.00' }}
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-green-700">
                                Rs. {{ $fee ? number_format($fee->paid_amount, 2) : '0.00' }}
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-red-600">
                                Rs. {{ $fee ? number_format($fee->balance_amount, 2) : '0.00' }}
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-blue-600">
                                Rs. {{ number_format($record['advance_balance'], 2) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($fee)
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                        {{ $fee->erp_status }}
                                    </span>
                                    @if($fee->is_locked)
                                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium bg-gray-200 text-gray-600 ml-1">
                                            Locked
                                        </span>
                                    @endif
                                @else
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold bg-gray-100 text-gray-700">
                                        No Invoice
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('finance.fee-records.show', $student->id) }}"
                                       class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                        View
                                    </a>
                                    @if($fee && $fee->balance_amount > 0)
                                        <button type="button"
                                                onclick="openPaymentModal({{ $fee->id }}, '{{ $studentName }}', {{ $fee->balance_amount }})"
                                                class="text-sm text-green-600 hover:text-green-800 font-medium">
                                            Collect
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-gray-500">
                                No students enrolled in this class.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('admin.finance.partials.payment-modal')
@endsection

@section('scripts')
<script>
function openPaymentModal(feeId, studentName, balance) {
    document.getElementById('payment-fee-id').value = feeId;
    document.getElementById('payment-student-name').textContent = studentName;
    document.getElementById('payment-balance').textContent = 'Rs. ' + parseFloat(balance).toLocaleString('en-IN', {minimumFractionDigits: 2});
    document.getElementById('payment-amount').max = balance;
    document.getElementById('payment-modal').classList.remove('hidden');
}

function closePaymentModal() {
    document.getElementById('payment-modal').classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('payment-amount');
    const balanceDisplay = document.getElementById('payment-balance');

    if (amountInput) {
        amountInput.addEventListener('input', function() {
            const balance = parseFloat(amountInput.max) || 0;
            const amount = parseFloat(this.value) || 0;

            if (amount > balance) {
                this.value = balance;
                document.getElementById('payment-excess-warning').classList.remove('hidden');
            } else {
                document.getElementById('payment-excess-warning').classList.add('hidden');
            }
        });
    }

    // Close modal on backdrop click
    const modal = document.getElementById('payment-modal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closePaymentModal();
            }
        });
    }
});
</script>
@endsection