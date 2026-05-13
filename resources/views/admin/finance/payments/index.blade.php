@extends('layouts.admin.layout')

@section('title', 'Fee Payments')

@section('content')

<div class="relative">
    {{-- Header --}}
    <div class="flex flex-wrap lg:flex-row justify-between my-3">
        <div>
            <h1 class="admin-h1 my-3">Payment Collection</h1>
        </div>
        <div class="flex items-center gap-2">
            <form method="GET" class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search invoice or student..."
                       class="rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-0 w-64 pl-8">
                <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </form>
        </div>
    </div>

    @include('admin.finance.partials.tabs')

    {{-- Table --}}
    <div class="mt-6 bg-white custom-shadow border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Invoice</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Student Identity</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Class</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wide text-gray-600">Total</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wide text-gray-600">Paid</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wide text-gray-600">Balance</th>
                        <th class="px-5 py-4 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">Status</th>
                        <th class="px-5 py-4 text-center text-xs font-semibold uppercase tracking-wide text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($fees as $fee)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-3">
                                <div class="font-semibold text-blue-700 text-xs">{{ $fee->invoice_no }}</div>
                                <div class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">{{ $fee->billing_cycle ?? $fee->payment_period ?? 'Monthly' }}</div>
                            </td>
                            <td class="px-5 py-3">
                                <div class="font-medium text-gray-900 text-xs">
                                    {{ ($fee->student->userprofile->firstname ?? '') . ' ' . ($fee->student->userprofile->lastname ?? '') ?: ($fee->student->name ?? 'N/A') }}
                                </div>
                                <div class="text-[10px] text-gray-400 font-mono mt-0.5 uppercase tracking-tighter">ID: {{ $fee->user_id }}</div>
                            </td>
                            <td class="px-5 py-3 text-xs text-gray-600">
                                {{ $fee->studentAcademic->standardLink->standard->name ?? '-' }}
                                @if($fee->studentAcademic->standardLink->section)
                                    ({{ $fee->studentAcademic->standardLink->section->name }})
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right font-semibold text-gray-900 text-xs">
                                Rs. {{ number_format($fee->total_amount, 0) }}
                            </td>
                            <td class="px-5 py-3 text-right font-semibold text-green-700 text-xs">
                                Rs. {{ number_format($fee->paid_amount, 0) }}
                            </td>
                            <td class="px-5 py-3 text-right font-semibold text-red-600 text-xs">
                                Rs. {{ number_format($fee->balance, 0) }}
                            </td>
                            <td class="px-5 py-3 text-center">
                                @if($fee->balance <= 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700 uppercase tracking-wider">Paid</span>
                                @elseif($fee->paid_amount > 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-yellow-100 text-yellow-700 uppercase tracking-wider">Partial</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700 uppercase tracking-wider">Unpaid</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center">
                                <a href="{{ route('finance.payments.create', $fee->id) }}"
                                   class="inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-[10px] font-bold uppercase tracking-wider transition rounded shadow-sm">
                                    Collect
                                </a>
                            </td>
                        </tr>
                    @empty
                        @include('admin.finance.partials.table-empty', ['message' => 'No invoices found matching your criteria.'])
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $fees->links() }}
    </div>
</div>

@endsection
