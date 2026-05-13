@extends('layouts.admin.layout')

@section('title', "Fee Record - {$student->userprofile->firstname ?? ''} {$student->userprofile->lastname ?? ''}")

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
            <a href="{{ route('finance.fee-records.index') }}" class="hover:text-blue-600">Fee Records</a>
            <span>/</span>
            <span>{{ $student->userprofile->firstname ?? '' }} {{ $student->userprofile->lastname ?? '' }}</span>
        </div>
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    {{ $student->userprofile->firstname ?? '' }} {{ $student->userprofile->lastname ?? '' }}
                </h1>
                <p class="text-gray-600 mt-1">
                    {{ $student->studentAcademic->standardLink->standard->name ?? '' }}
                    {{ $student->studentAcademic->standardLink->section->name ?? '' }}
                    | {{ $academicYear->name }}
                </p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Student ID</p>
                <p class="font-mono text-gray-900">#{{ $student->id }}</p>
            </div>
        </div>
    </div>

    @include('admin.finance.partials.navigation')

    {{-- Lifetime Summary Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Total Demand</p>
            <p class="text-2xl font-bold text-gray-900">Rs. {{ number_format($lifetimeSummary['total_demand'], 0) }}</p>
        </div>
        <div class="bg-green-50 rounded-lg border border-green-200 p-4">
            <p class="text-sm text-green-600">Total Paid</p>
            <p class="text-2xl font-bold text-green-700">Rs. {{ number_format($lifetimeSummary['total_paid'], 0) }}</p>
        </div>
        <div class="bg-red-50 rounded-lg border border-red-200 p-4">
            <p class="text-sm text-red-600">Total Pending</p>
            <p class="text-2xl font-bold text-red-700">Rs. {{ number_format($lifetimeSummary['total_pending'], 0) }}</p>
        </div>
        <div class="bg-blue-50 rounded-lg border border-blue-200 p-4">
            <p class="text-sm text-blue-600">Advance Credit</p>
            <p class="text-2xl font-bold text-blue-700">Rs. {{ number_format($lifetimeSummary['advance_balance'], 2) }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Paid Invoices</p>
            <p class="text-2xl font-bold text-gray-900">{{ $lifetimeSummary['paid_invoices'] }}/{{ $lifetimeSummary['total_invoices'] }}</p>
        </div>
    </div>

    {{-- Tabs Navigation --}}
    <div class="border-b border-gray-200 mb-6">
        <nav class="flex gap-6">
            <button type="button" class="tab-btn pb-3 border-b-2 border-blue-600 text-blue-600 font-medium"
                    data-tab="monthly-fees">
                Monthly Fees
            </button>
            <button type="button" class="tab-btn pb-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700"
                    data-tab="advance-ledger">
                Advance Ledger
            </button>
            <button type="button" class="tab-btn pb-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700"
                    data-tab="payment-history">
                All Payments
            </button>
        </nav>
    </div>

    {{-- Monthly Fees Tab --}}
    <div id="tab-monthly-fees" class="tab-content">
        <div class="space-y-4">
            @forelse($monthlyFees as $monthly)
                @php
                    $fee = $monthly['fee'];
                    $statusClass = $fee->erp_status_badge_class ?? 'bg-gray-100 text-gray-700 border border-gray-200';
                @endphp
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                    {{-- Month Header --}}
                    <div class="flex items-center justify-between px-5 py-3 bg-gray-50 border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $monthly['month_name'] }}</h3>
                            @if($fee && $fee->invoice_no)
                                <span class="font-mono text-sm text-blue-700">{{ $fee->invoice_no }}</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-3">
                            @if($fee)
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                    {{ $fee->erp_status }}
                                </span>
                                @if($fee->is_locked)
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium bg-gray-200 text-gray-600">
                                        Locked
                                    </span>
                                @endif
                            @endif
                            <span class="text-sm text-gray-500">Due: {{ $fee ? optional($fee->due_date)->format('d M Y') : '-' }}</span>
                        </div>
                    </div>

                    {{-- Month Content --}}
                    @if($fee)
                        <div class="p-5">
                            {{-- Fee Items --}}
                            @if($fee->items->count() > 0)
                                <div class="mb-4">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-2 uppercase tracking-wide">Fee Breakdown</h4>
                                    <div class="space-y-2">
                                        @foreach($fee->items as $item)
                                            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                                                <div>
                                                    <span class="font-medium text-gray-900">{{ $item->category->name ?? 'Uncategorized' }}</span>
                                                    @if((float) $item->fine_amount > 0)
                                                        <span class="text-xs text-orange-600 ml-2">+ Fine: Rs. {{ number_format($item->fine_amount, 2) }}</span>
                                                    @endif
                                                </div>
                                                <span class="font-semibold text-gray-900">Rs. {{ number_format($item->total, 2) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Summary --}}
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 py-3 bg-gray-50 rounded-lg px-4 mb-4">
                                <div>
                                    <p class="text-xs text-gray-500">Total</p>
                                    <p class="text-lg font-bold text-gray-900">Rs. {{ number_format($fee->total_amount, 2) }}</p>
                                </div>
                                <div class="text-green-700">
                                    <p class="text-xs text-green-600">Paid</p>
                                    <p class="text-lg font-bold">Rs. {{ number_format($fee->paid_amount, 2) }}</p>
                                </div>
                                <div class="text-red-600">
                                    <p class="text-xs text-red-500">Balance</p>
                                    <p class="text-lg font-bold">Rs. {{ number_format($fee->balance_amount, 2) }}</p>
                                </div>
                                <div class="text-blue-700">
                                    <p class="text-xs text-blue-500">Advance</p>
                                    <p class="text-lg font-bold">Rs. {{ number_format($fee->advance_amount, 2) }}</p>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center gap-3">
                                <button type="button"
                                        onclick="openPaymentModal({{ $fee->id }}, '{{ $student->userprofile->firstname ?? '' }}', {{ $fee->balance_amount }})"
                                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 font-medium text-sm"
                                        @if($fee->balance_amount <= 0) disabled @endif>
                                    Record Payment
                                </button>

                                @if($advanceBalance > 0 && $fee->balance_amount > 0)
                                    <form action="{{ route('finance.fee-records.apply-advance', $fee->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="amount" value="{{ min($advanceBalance, $fee->balance_amount) }}">
                                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium text-sm">
                                            Apply Advance (Rs. {{ number_format(min($advanceBalance, $fee->balance_amount), 2) }})
                                        </button>
                                    </form>
                                @endif

                                <form action="{{ route('finance.fee-records.toggle-lock', $fee->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 font-medium text-sm">
                                        {{ $fee->is_locked ? 'Unlock' : 'Lock' }} Invoice
                                    </button>
                                </form>

                                @if($fee->payments->count() > 0)
                                    <button type="button"
                                            onclick="showPaymentHistory({{ $fee->id }})"
                                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 font-medium text-sm">
                                        View Receipts ({{ $fee->payments->count() }})
                                    </button>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="p-5 text-center text-gray-500">
                            <p>No invoice generated for this month.</p>
                            <button type="button" class="mt-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium text-sm">
                                Generate Invoice
                            </button>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white rounded-lg border border-gray-200 p-12 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p>No fee records found for this student.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Advance Ledger Tab --}}
    <div id="tab-advance-ledger" class="tab-content hidden">
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="px-5 py-3 bg-gray-50 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Advance Credit Ledger</h3>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Current Balance</p>
                        <p class="text-xl font-bold text-blue-600">Rs. {{ number_format($advanceBalance, 2) }}</p>
                    </div>
                </div>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Description</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-600">Amount</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-600">Balance After</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($advanceLedger as $entry)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $entry->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium
                                    {{ $entry->transaction_type === 'credit' || $entry->transaction_type === 'advance_received' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ ucfirst(str_replace('_', ' ', $entry->transaction_type)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $entry->description ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm font-semibold text-right {{ $entry->amount >= 0 ? 'text-green-700' : 'text-red-600' }}">
                                {{ $entry->amount >= 0 ? '+' : '' }}Rs. {{ number_format($entry->amount, 2) }}
                            </td>
                            <td class="px-4 py-3 text-sm font-semibold text-right text-gray-900">
                                Rs. {{ number_format($entry->balance_after, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">No ledger entries found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- All Payments Tab --}}
    <div id="tab-payment-history" class="tab-content hidden">
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Receipt No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Month</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Method</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-600">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @php
                        $allPayments = $monthlyFees->pluck('fee.payments')->filter()->flatten();
                    @endphp
                    @forelse($allPayments as $payment)
                        <tr>
                            <td class="px-4 py-3 font-mono text-sm text-blue-700">{{ $payment->receipt_no }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ optional($payment->payment_date)->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $payment->fee->display_period ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ ucfirst($payment->payment_method) }}</td>
                            <td class="px-4 py-3 text-sm font-semibold text-right text-green-700">Rs. {{ number_format($payment->amount, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $payment->remarks ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">No payment records found.</td>
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
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const tabName = this.dataset.tab;

            tabBtns.forEach(b => {
                b.classList.remove('border-blue-600', 'text-blue-600');
                b.classList.add('border-transparent', 'text-gray-500');
            });
            this.classList.remove('border-transparent', 'text-gray-500');
            this.classList.add('border-blue-600', 'text-blue-600');

            tabContents.forEach(content => {
                content.classList.add('hidden');
            });
            document.getElementById('tab-' + tabName).classList.remove('hidden');
        });
    });
});

function openPaymentModal(feeId, studentName, balance) {
    document.getElementById('payment-fee-id').value = feeId;
    document.getElementById('payment-student-name').textContent = studentName;
    document.getElementById('payment-balance').textContent = 'Rs. ' + parseFloat(balance).toLocaleString('en-IN', {minimumFractionDigits: 2});
    document.getElementById('payment-amount').max = balance;
    document.getElementById('payment-modal').classList.remove('hidden');
}

function closePaymentModal() {
    document.getElementById('payment-modal').classList.add('hidden');
    document.getElementById('payment-excess-warning').classList.add('hidden');
}

function showPaymentHistory(feeId) {
    const row = document.querySelector(`tr[data-fee-id="${feeId}"]`);
    // Could implement quick view modal here
    alert('Payment history for fee #' + feeId);
}

document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('payment-amount');
    if (amountInput) {
        amountInput.addEventListener('input', function() {
            const balance = parseFloat(this.max) || 0;
            const amount = parseFloat(this.value) || 0;

            if (amount > balance) {
                this.value = balance;
                document.getElementById('payment-excess-warning').classList.remove('hidden');
            } else {
                document.getElementById('payment-excess-warning').classList.add('hidden');
            }
        });
    }

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