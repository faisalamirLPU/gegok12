@extends('layouts.admin.layout')

@section(
    'title',
    'Fee Records - '
    . ($classLink->standard->name ?? '')
    . ' '
    . ($classLink->section->name ?? '')
)

@section('content')
<div class="relative">
    {{-- Header --}}
    <div class="flex flex-wrap lg:flex-row justify-between my-3">
        <div>
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                <a href="{{ route('finance.fee-records.index') }}" class="hover:text-blue-600">Fee Records</a>
                <span>/</span>
                <span>{{ $classLink->standard->name ?? '' }} {{ $classLink->section->name ?? '' }}</span>
            </div>
            <h1 class="admin-h1">
                {{ $classLink->standard->name ?? '' }} {{ $classLink->section->name ?? '' }} ({{ $academicYear->name }})
            </h1>
        </div>
    </div>

    @include('admin.finance.partials.tabs')

    {{-- Month Selector --}}
    <div class="bg-white custom-shadow border p-4 mb-4 mt-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <input type="hidden" name="class_id" value="{{ $classLink->id }}">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wider">Select Month</label>
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
                <span class="text-sm font-semibold">Filter</span>
            </button>
        </form>
    </div>

    {{-- Class Summary --}}
    <div class="flex flex-wrap -mx-1">
        <div class="w-full lg:w-1/6 md:w-1/3 px-1 my-2">
            <div class="bg-white custom-shadow p-4 border h-full">
                <p class="text-xs font-medium text-gray-500">Total Students</p>
                <p class="text-xl font-bold text-gray-800">{{ $summary['total_students'] }}</p>
            </div>
        </div>
        <div class="w-full lg:w-1/6 md:w-1/3 px-1 my-2">
            <div class="bg-white custom-shadow p-4 border border-green-100 h-full">
                <p class="text-xs font-medium text-green-600">Paid</p>
                <p class="text-xl font-bold text-green-700">{{ $summary['paid_count'] }}</p>
            </div>
        </div>
        <div class="w-full lg:w-1/6 md:w-1/3 px-1 my-2">
            <div class="bg-white custom-shadow p-4 border border-yellow-100 h-full">
                <p class="text-xs font-medium text-yellow-600">Partial</p>
                <p class="text-xl font-bold text-yellow-700">{{ $summary['partial_count'] }}</p>
            </div>
        </div>
        <div class="w-full lg:w-1/6 md:w-1/3 px-1 my-2">
            <div class="bg-white custom-shadow p-4 border border-red-100 h-full">
                <p class="text-xs font-medium text-red-600">Pending</p>
                <p class="text-xl font-bold text-red-700">{{ $summary['pending_count'] }}</p>
            </div>
        </div>
        <div class="w-full lg:w-1/6 md:w-1/3 px-1 my-2">
            <div class="bg-white custom-shadow p-4 border border-blue-100 h-full">
                <p class="text-xs font-medium text-blue-600">No Invoice</p>
                <p class="text-xl font-bold text-blue-700">{{ $summary['no_invoice_count'] }}</p>
            </div>
        </div>
        <div class="w-full lg:w-1/6 md:w-1/3 px-1 my-2">
            <div class="bg-white custom-shadow p-4 border h-full">
                <p class="text-xs font-medium text-gray-500">Total Demand</p>
                <p class="text-xl font-bold text-gray-800">Rs. {{ number_format($summary['total_demand'], 0) }}</p>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap -mx-1 mt-2 mb-4">
        <div class="w-full lg:w-1/3 md:w-1/3 px-1 my-2">
            <div class="bg-white custom-shadow p-4 border border-green-100 h-full">
                <p class="text-xs font-medium text-green-600">Collected</p>
                <p class="text-xl font-bold text-green-700">Rs. {{ number_format($summary['total_collected'], 0) }}</p>
            </div>
        </div>
        <div class="w-full lg:w-1/3 md:w-1/3 px-1 my-2">
            <div class="bg-white custom-shadow p-4 border border-red-100 h-full">
                <p class="text-xs font-medium text-red-600">Pending Amount</p>
                <p class="text-xl font-bold text-red-700">Rs. {{ number_format($summary['total_pending'], 0) }}</p>
            </div>
        </div>
        <div class="w-full lg:w-1/3 md:w-1/3 px-1 my-2">
            <div class="bg-white custom-shadow p-4 border border-blue-100 h-full">
                <p class="text-xs font-medium text-blue-600">Collection %</p>
                <p class="text-xl font-bold text-blue-700">
                    {{ $summary['total_demand'] > 0 ? round(($summary['total_collected'] / $summary['total_demand']) * 100) : 0 }}%
                </p>
            </div>
        </div>
    </div>

    {{-- Student List --}}
    <div class="bg-white custom-shadow border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b">
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
                <tbody class="divide-y divide-gray-100">
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
                                <a href="{{ route('finance.fee-records.show', $student->id) }}" class="font-medium text-gray-900 hover:text-blue-600 transition text-xs">
                                    {{ $studentName }}
                                </a>
                                <div class="text-[10px] text-gray-400">{{ $student->name }}</div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($fee)
                                    <span class="font-mono text-blue-700 text-xs">{{ $fee->invoice_no }}</span>
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-xs text-gray-800">
                                Rs. {{ $fee ? number_format($fee->total_amount, 0) : '0' }}
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-green-700 text-xs">
                                Rs. {{ $fee ? number_format($fee->paid_amount, 0) : '0' }}
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-red-600 text-xs">
                                Rs. {{ $fee ? number_format($fee->balance_amount, 0) : '0' }}
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-blue-600 text-xs">
                                Rs. {{ number_format($record['advance_balance'], 0) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($fee)
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $statusClass }}">
                                        {{ $fee->erp_status }}
                                    </span>
                                    @if($fee->is_locked)
                                        <span class="inline-flex rounded-full px-1.5 py-0.5 text-[10px] font-medium bg-gray-200 text-gray-600 ml-1">
                                            Locked
                                        </span>
                                    @endif
                                @else
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-semibold bg-gray-100 text-gray-400">
                                        No Invoice
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('finance.fee-records.show', $student->id) }}"
                                       class="text-[10px] text-blue-600 hover:underline font-bold uppercase tracking-wider">
                                        View
                                    </a>
                                    @if($fee && $fee->balance_amount > 0)
                                        <button type="button"
                                                onclick="openPaymentModal({{ $fee->id }}, '{{ $studentName }}', {{ $fee->balance_amount }})"
                                                class="text-[10px] text-green-600 hover:underline font-bold uppercase tracking-wider">
                                            Collect
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-gray-400 text-xs">
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
