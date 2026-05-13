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

    {{-- Header --}}
    <div class="flex flex-wrap lg:flex-row justify-between my-3">

        <div>

            <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">

                <a href="{{ route('finance.fee-records.index') }}"
                   class="hover:text-blue-600">

                    Fee Records

                </a>

                <span>/</span>

                <span>

                    {{ $student->userprofile->firstname ?? '' }}
                    {{ $student->userprofile->lastname ?? '' }}

                </span>

            </div>

            <h1 class="admin-h1">

                {{ $student->userprofile->firstname ?? '' }}
                {{ $student->userprofile->lastname ?? '' }}

            </h1>

        </div>

        <div class="text-right">

            <p class="text-sm text-gray-500">
                Student ID
            </p>

            <p class="font-mono text-gray-900 font-bold">
                #{{ $student->id }}
            </p>

        </div>

    </div>

    @include('admin.finance.partials.tabs')

    {{-- Summary Cards --}}
    <div class="flex flex-wrap -mx-1 mt-4">

        <div class="w-full lg:w-1/5 md:w-1/2 px-1 my-2">

            <div class="bg-white custom-shadow p-4 border h-full">

                <p class="text-xs font-medium text-gray-500">
                    Total Demand
                </p>

                <p class="text-xl font-bold text-gray-800">

                    Rs.
                    {{ number_format($lifetimeSummary['total_demand'], 0) }}

                </p>

            </div>

        </div>

        <div class="w-full lg:w-1/5 md:w-1/2 px-1 my-2">

            <div class="bg-white custom-shadow p-4 border border-green-100 h-full">

                <p class="text-xs font-medium text-green-600">
                    Total Paid
                </p>

                <p class="text-xl font-bold text-green-700">

                    Rs.
                    {{ number_format($lifetimeSummary['total_paid'], 0) }}

                </p>

            </div>

        </div>

        <div class="w-full lg:w-1/5 md:w-1/2 px-1 my-2">

            <div class="bg-white custom-shadow p-4 border border-red-100 h-full">

                <p class="text-xs font-medium text-red-600">
                    Total Pending
                </p>

                <p class="text-xl font-bold text-red-700">

                    Rs.
                    {{ number_format($lifetimeSummary['total_pending'], 0) }}

                </p>

            </div>

        </div>

        <div class="w-full lg:w-1/5 md:w-1/2 px-1 my-2">

            <div class="bg-white custom-shadow p-4 border border-blue-100 h-full">

                <p class="text-xs font-medium text-blue-600">
                    Advance Credit
                </p>

                <p class="text-xl font-bold text-blue-700">

                    Rs.
                    {{ number_format($lifetimeSummary['advance_balance'], 2) }}

                </p>

            </div>

        </div>

        <div class="w-full lg:w-1/5 md:w-1/2 px-1 my-2">

            <div class="bg-white custom-shadow p-4 border h-full">

                <p class="text-xs font-medium text-gray-500">
                    Paid Invoices
                </p>

                <p class="text-xl font-bold text-gray-800">

                    {{ $lifetimeSummary['paid_invoices'] }}/{{ $lifetimeSummary['total_invoices'] }}

                </p>

            </div>

        </div>

    </div>

    {{-- Monthly Fees --}}
    <div class="mt-5 space-y-4">

        @forelse($monthlyFees as $monthly)

            @php
                $fee = $monthly['fee'];
                $statusClass = $fee->erp_status_badge_class
                    ?? 'bg-gray-100 text-gray-700 border border-gray-200';
            @endphp

            <div class="bg-white custom-shadow border overflow-hidden">

                {{-- Header --}}
                <div class="flex items-center justify-between px-5 py-3 bg-gray-50 border-b">

                    <div class="flex items-center gap-3">

                        <h3 class="text-base font-bold text-gray-800 uppercase tracking-tight">

                            {{ $monthly['month_name'] }}

                        </h3>

                        @if($fee && $fee->invoice_no)

                            <span class="font-mono text-xs text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-100">

                                {{ $fee->invoice_no }}

                            </span>

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

                    </div>

                </div>

                {{-- Content --}}
                @if($fee)

                    <div class="p-5">

                        {{-- Fee Breakdown --}}
                        @if($fee->items->count() > 0)

                            <div class="mb-4">

                                <h4 class="text-[10px] font-bold text-gray-400 mb-2 uppercase tracking-widest">

                                    Fee Breakdown

                                </h4>

                                <div class="space-y-2">

                                    @foreach($fee->items as $item)

                                        <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">

                                            <div>

                                                <span class="text-xs font-semibold text-gray-700">

                                                    {{ $item->remarks ?: ($item->category->name ?? 'Uncategorized') }}

                                                </span>

                                            </div>

                                            <span class="text-xs font-bold text-gray-800">

                                                Rs. {{ number_format($item->total, 0) }}

                                            </span>

                                        </div>

                                    @endforeach

                                </div>

                            </div>

                        @endif

                        {{-- Summary --}}
                        <div class="flex flex-wrap -mx-1 py-3 bg-gray-50 rounded border px-4 mb-4">

                            <div class="w-1/4 px-1">

                                <p class="text-[10px] text-gray-400 font-bold uppercase">
                                    Total
                                </p>

                                <p class="text-sm font-bold text-gray-800">

                                    Rs. {{ number_format($fee->total_amount, 0) }}

                                </p>

                            </div>

                            <div class="w-1/4 px-1">

                                <p class="text-[10px] text-green-500 font-bold uppercase">
                                    Paid
                                </p>

                                <p class="text-sm font-bold text-green-700">

                                    Rs. {{ number_format($fee->paid_amount, 0) }}

                                </p>

                            </div>

                            <div class="w-1/4 px-1">

                                <p class="text-[10px] text-red-400 font-bold uppercase">
                                    Balance
                                </p>

                                <p class="text-sm font-bold text-red-600">

                                    Rs. {{ number_format($fee->balance_amount, 0) }}

                                </p>

                            </div>

                            <div class="w-1/4 px-1">

                                <p class="text-[10px] text-blue-400 font-bold uppercase">
                                    Advance
                                </p>

                                <p class="text-sm font-bold text-blue-700">

                                    Rs. {{ number_format($fee->advance_amount, 0) }}

                                </p>

                            </div>

                        </div>

                        {{-- Actions --}}
                        <div class="flex flex-wrap items-center gap-2">

                            {{-- Record Payment --}}
                            <button
                                type="button"

                                onclick="openPaymentModal(
                                    {{ $fee->id }},
                                    '{{ addslashes(($student->userprofile->firstname ?? '') . ' ' . ($student->userprofile->lastname ?? '')) }}',
                                    {{ $fee->balance_amount }}
                                )"

                                class="no-underline text-white px-4 flex items-center py-1.5 justify-center rounded
                                {{ $fee->balance_amount <= 0
                                    ? 'bg-gray-400 cursor-not-allowed'
                                    : 'custom-green hover:opacity-90'
                                }}"

                                {{ $fee->balance_amount <= 0 ? 'disabled' : '' }}
                            >

                                <span class="text-xs font-bold uppercase tracking-wider">
                                    Record Payment
                                </span>

                            </button>

                            {{-- Lock --}}
                            <form
                                action="{{ route('finance.fee-records.toggle-lock', $fee->id) }}"
                                method="POST"
                                class="inline"
                            >

                                @csrf

                                <button
                                    type="submit"
                                    class="no-underline text-gray-700 px-4 flex items-center bg-gray-100 border rounded py-1.5 justify-center hover:bg-gray-200 transition"
                                >

                                    <span class="text-xs font-bold uppercase tracking-wider">

                                        {{ $fee->is_locked ? 'Unlock' : 'Lock' }} Invoice

                                    </span>

                                </button>

                            </form>

                            {{-- Receipts --}}
                            @if($fee->payments->count() > 0)

                                <a
                                    href="{{ route('finance.receipts.index', $fee->id) }}"
                                    target="_blank"
                                    class="no-underline text-blue-600 px-4 flex items-center bg-blue-50 border border-blue-100 rounded py-1.5 justify-center hover:bg-blue-100 transition"
                                >

                                    <span class="text-xs font-bold uppercase tracking-wider">

                                        Receipts ({{ $fee->payments->count() }})

                                    </span>

                                </a>

                                {{-- Fee Slip --}}
                                <a
                                    href="{{ route('finance.fee-slip', $fee->id) }}"
                                    target="_blank"
                                    class="no-underline text-purple-700 px-4 flex items-center bg-purple-50 border border-purple-100 rounded py-1.5 justify-center hover:bg-purple-100 transition"
                                >

                                    <span class="text-xs font-bold uppercase tracking-wider">

                                        Fee Slip

                                    </span>

                                </a>

                            @endif

                        </div>

                    </div>

                @endif

            </div>

        @empty

            <div class="bg-white custom-shadow border p-12 text-center text-gray-400">

                <p class="text-sm">
                    No fee records found for this student.
                </p>

            </div>

        @endforelse

    </div>

</div>

@include('admin.finance.partials.payment-modal')

@endsection

@section('scripts')

<script>

function openPaymentModal(feeId, studentName, balance)
{
    document.getElementById('payment-fee-id').value = feeId;

    document.getElementById('payment-student-name').textContent = studentName;

    document.getElementById('payment-balance').textContent =
        'Rs. ' + parseFloat(balance).toLocaleString('en-IN', {
            minimumFractionDigits: 2
        });

    document.getElementById('payment-amount').max = balance;

    document.getElementById('payment-modal').classList.remove('hidden');
}

function closePaymentModal()
{
    document.getElementById('payment-modal').classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', function() {

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