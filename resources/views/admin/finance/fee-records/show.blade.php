@extends('layouts.admin.layout')

@section(
    'title',
    'Fee Record - '
    . ($student->userprofile->firstname ?? '')
    . ' '
    . ($student->userprofile->lastname ?? '')
)

@section('content')
<div class="relative">
    <div class="flex flex-wrap lg:flex-row justify-between my-3">
        <div>
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                <a href="{{ route('finance.fee-records.index') }}" class="hover:text-blue-600">Fee Records</a>
                <span>/</span>
                <span>{{ $student->userprofile->firstname ?? '' }} {{ $student->userprofile->lastname ?? '' }}</span>
            </div>
            <h1 class="admin-h1">
                {{ $student->userprofile->firstname ?? '' }} {{ $student->userprofile->lastname ?? '' }}
            </h1>
        </div>
        <div class="text-right">
            <p class="text-sm text-gray-500">Student ID</p>
            <p class="font-mono text-gray-900 font-bold">#{{ $student->id }}</p>
        </div>
    </div>

    @include('admin.finance.partials.tabs')

    {{-- Lifetime Summary Cards --}}
    <div class="flex flex-wrap -mx-1 mt-4">
        <div class="w-full lg:w-1/5 md:w-1/2 px-1 my-2">
            <div class="bg-white custom-shadow p-4 border h-full">
                <p class="text-xs font-medium text-gray-500">Total Demand</p>
                <p class="text-xl font-bold text-gray-800">Rs. {{ number_format($lifetimeSummary['total_demand'], 0) }}</p>
            </div>
        </div>
        <div class="w-full lg:w-1/5 md:w-1/2 px-1 my-2">
            <div class="bg-white custom-shadow p-4 border border-green-100 h-full">
                <p class="text-xs font-medium text-green-600">Total Paid</p>
                <p class="text-xl font-bold text-green-700">Rs. {{ number_format($lifetimeSummary['total_paid'], 0) }}</p>
            </div>
        </div>
        <div class="w-full lg:w-1/5 md:w-1/2 px-1 my-2">
            <div class="bg-white custom-shadow p-4 border border-red-100 h-full">
                <p class="text-xs font-medium text-red-600">Total Pending</p>
                <p class="text-xl font-bold text-red-700">Rs. {{ number_format($lifetimeSummary['total_pending'], 0) }}</p>
            </div>
        </div>
        <div class="w-full lg:w-1/5 md:w-1/2 px-1 my-2">
            <div class="bg-white custom-shadow p-4 border border-blue-100 h-full">
                <p class="text-xs font-medium text-blue-600">Advance Credit</p>
                <p class="text-xl font-bold text-blue-700">Rs. {{ number_format($lifetimeSummary['advance_balance'], 2) }}</p>
            </div>
        </div>
        <div class="w-full lg:w-1/5 md:w-1/2 px-1 my-2">
            <div class="bg-white custom-shadow p-4 border h-full">
                <p class="text-xs font-medium text-gray-500">Paid Invoices</p>
                <p class="text-xl font-bold text-gray-800">{{ $lifetimeSummary['paid_invoices'] }}/{{ $lifetimeSummary['total_invoices'] }}</p>
            </div>
        </div>
    </div>

    {{-- Tabs Navigation --}}
    <div class="border-b border-gray-200 mb-4 mt-2">
        <nav class="flex gap-6">
            <button type="button" class="tab-btn pb-2 border-b-2 border-blue-600 text-blue-600 font-bold text-xs uppercase tracking-wider"
                    data-tab="monthly-fees">
                Monthly Fees
            </button>
            <button type="button" class="tab-btn pb-2 border-b-2 border-transparent text-gray-400 hover:text-gray-600 font-bold text-xs uppercase tracking-wider"
                    data-tab="advance-ledger">
                Advance Ledger
            </button>
            <button type="button" class="tab-btn pb-2 border-b-2 border-transparent text-gray-400 hover:text-gray-600 font-bold text-xs uppercase tracking-wider"
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
                <div class="bg-white custom-shadow border overflow-hidden">
                    {{-- Month Header --}}
                    <div class="flex items-center justify-between px-5 py-3 bg-gray-50 border-b">
                        <div class="flex items-center gap-3">
                            <h3 class="text-base font-bold text-gray-800 uppercase tracking-tight">{{ $monthly['month_name'] }}</h3>
                            @if($fee && $fee->invoice_no)
                                <span class="font-mono text-xs text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-100">{{ $fee->invoice_no }}</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-4">
                            @if($fee)
                                <span class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $statusClass }}">
                                    {{ $fee->erp_status }}
                                </span>
                                @if($fee->is_locked)
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-medium bg-gray-200 text-gray-600">
                                        Locked
                                    </span>
                                @endif
                            @endif
                            <span class="text-[10px] text-gray-400 font-medium uppercase">Due: {{ $fee ? optional($fee->due_date)->format('d M Y') : '-' }}</span>
                        </div>
                    </div>

                    {{-- Month Content --}}
                    @if($fee)
                        <div class="p-5">
                            {{-- Fee Items --}}
                            @if($fee->items->count() > 0)
                                <div class="mb-4">
                                    <h4 class="text-[10px] font-bold text-gray-400 mb-2 uppercase tracking-widest">Fee Breakdown</h4>
                                    <div class="space-y-2">
                                        @foreach($fee->items as $item)
                                            <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                                                <div>
                                                    <span class="text-xs font-semibold text-gray-700">{{ $item->category->name ?? 'Uncategorized' }}</span>
                                                    @if((float) $item->fine_amount > 0)
                                                        <span class="text-[10px] text-red-500 ml-2 font-medium">+ Fine: Rs. {{ number_format($item->fine_amount, 0) }}</span>
                                                    @endif
                                                </div>
                                                <span class="text-xs font-bold text-gray-800">Rs. {{ number_format($item->total, 0) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Summary --}}
                            <div class="flex flex-wrap -mx-1 py-3 bg-gray-50/50 rounded border px-4 mb-4">
                                <div class="w-1/4 px-1">
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">Total</p>
                                    <p class="text-sm font-bold text-gray-800">Rs. {{ number_format($fee->total_amount, 0) }}</p>
                                </div>
                                <div class="w-1/4 px-1">
                                    <p class="text-[10px] text-green-500 font-bold uppercase tracking-tighter">Paid</p>
                                    <p class="text-sm font-bold text-green-700">Rs. {{ number_format($fee->paid_amount, 0) }}</p>
                                </div>
                                <div class="w-1/4 px-1">
                                    <p class="text-[10px] text-red-400 font-bold uppercase tracking-tighter">Balance</p>
                                    <p class="text-sm font-bold text-red-600">Rs. {{ number_format($fee->balance_amount, 0) }}</p>
                                </div>
                                <div class="w-1/4 px-1">
                                    <p class="text-[10px] text-blue-400 font-bold uppercase tracking-tighter">Advance</p>
                                    <p class="text-sm font-bold text-blue-700">Rs. {{ number_format($fee->advance_amount, 0) }}</p>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex flex-wrap items-center gap-2">
                                <button type="button"
                                        onclick="openPaymentModal({{ $fee->id }}, '{{ $student->userprofile->firstname ?? '' }}', {{ $fee->balance_amount }})"
                                        class="no-underline text-white px-4 flex items-center custom-green py-1.5 justify-center @if($fee->balance_amount <= 0) opacity-50 cursor-not-allowed @endif"
                                        @if($fee->balance_amount <= 0) disabled @endif>
                                    <span class="text-xs font-bold uppercase tracking-wider">Record Payment</span>
                                </button>

                                @if($advanceBalance > 0 && $fee->balance_amount > 0)
                                    <form action="{{ route('finance.fee-records.apply-advance', $fee->id) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="amount" value="{{ min($advanceBalance, $fee->balance_amount) }}">
                                        <button type="submit" class="no-underline text-white px-4 flex items-center bg-blue-600 rounded py-1.5 justify-center hover:bg-blue-700 transition">
                                            <span class="text-xs font-bold uppercase tracking-wider">Apply Advance (Rs. {{ number_format(min($advanceBalance, $fee->balance_amount), 0) }})</span>
                                        </button>
                                    </form>
                                @endif

                                <form action="{{ route('finance.fee-records.toggle-lock', $fee->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="no-underline text-gray-700 px-4 flex items-center bg-gray-100 border rounded py-1.5 justify-center hover:bg-gray-200 transition">
                                        <span class="text-xs font-bold uppercase tracking-wider">{{ $fee->is_locked ? 'Unlock' : 'Lock' }} Invoice</span>
                                    </button>
                                </form>

                                @if($fee->payments->count() > 0)
                                    <button type="button"
                                            onclick="showPaymentHistory({{ $fee->id }})"
                                            class="no-underline text-blue-600 px-4 flex items-center bg-blue-50 border border-blue-100 rounded py-1.5 justify-center hover:bg-blue-100 transition">
                                        <span class="text-xs font-bold uppercase tracking-wider">Receipts ({{ $fee->payments->count() }})</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="p-10 text-center">
                            <p class="text-sm text-gray-400 mb-3">No invoice generated for this month.</p>
                            <button type="button" class="no-underline text-white px-6 flex items-center custom-green py-1.5 justify-center mx-auto">
                                <span class="text-xs font-bold uppercase tracking-wider">Generate Invoice</span>
                            </button>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white custom-shadow border p-12 text-center text-gray-400">
                    <svg class="w-12 h-12 mx-auto text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="text-sm">No fee records found for this student.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Advance Ledger Tab --}}
    <div id="tab-advance-ledger" class="tab-content hidden">
        <div class="bg-white custom-shadow border overflow-hidden">
            <div class="px-5 py-3 bg-gray-50 border-b">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Advance Credit Ledger</h3>
                    <div class="text-right">
                        <p class="text-[10px] text-gray-400 font-bold uppercase">Current Balance</p>
                        <p class="text-xl font-bold text-blue-600">Rs. {{ number_format($advanceBalance, 0) }}</p>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b">
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Description</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-600">Amount</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-600">Balance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($advanceLedger as $entry)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-xs text-gray-600">{{ $entry->created_at->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-xs">
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-bold uppercase
                                        {{ $entry->transaction_type === 'credit' || $entry->transaction_type === 'advance_received' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ ucfirst(str_replace('_', ' ', $entry->transaction_type)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-600">{{ $entry->description ?? '-' }}</td>
                                <td class="px-4 py-3 text-xs font-bold text-right {{ $entry->amount >= 0 ? 'text-green-700' : 'text-red-600' }}">
                                    {{ $entry->amount >= 0 ? '+' : '' }}Rs. {{ number_format($entry->amount, 0) }}
                                </td>
                                <td class="px-4 py-3 text-xs font-bold text-right text-gray-800">
                                    Rs. {{ number_format($entry->balance_after, 0) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-400 text-xs">No ledger entries found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- All Payments Tab --}}
    <div id="tab-payment-history" class="tab-content hidden">
        <div class="bg-white custom-shadow border overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b">
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Receipt No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Month</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Method</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-600">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @php
                            $allPayments = $monthlyFees->pluck('fee.payments')->filter()->flatten();
                        @endphp
                        @forelse($allPayments as $payment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-mono text-xs text-blue-700 font-bold">{{ $payment->receipt_no }}</td>
                                <td class="px-4 py-3 text-xs text-gray-600">{{ optional($payment->payment_date)->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-xs text-gray-600 uppercase">{{ $payment->fee->display_period ?? '-' }}</td>
                                <td class="px-4 py-3 text-xs text-gray-600">{{ ucfirst($payment->payment_method) }}</td>
                                <td class="px-4 py-3 text-xs font-bold text-right text-green-700">Rs. {{ number_format($payment->amount, 0) }}</td>
                                <td class="px-4 py-3 text-xs text-gray-400 italic">{{ $payment->remarks ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-400 text-xs">No payment records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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
