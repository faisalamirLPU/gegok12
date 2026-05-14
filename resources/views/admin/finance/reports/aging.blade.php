@extends('layouts.admin.layout')

@section('title', 'Fee Aging Report')

@section('content')
<div class="relative">

    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Finance Reports</h1>
            <p class="text-sm text-gray-500">Enterprise Financial Analytics and Reporting</p>
        </div>
    </div>

    @include('admin.finance.reports.partials.tabs')

    {{-- Aging Buckets --}}
    <h2 class="text-lg font-bold text-gray-700 mb-3">Receivables Aging Buckets</h2>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded border shadow-sm p-4 border-l-4 border-yellow-400">
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">0 - 30 Days Overdue</p>
            <p class="text-2xl font-black text-gray-800 mt-1">Rs. {{ number_format($aging['0_30']['amount'], 0) }}</p>
            <p class="text-sm text-gray-400 mt-1">{{ $aging['0_30']['count'] }} Invoices</p>
        </div>
        
        <div class="bg-white rounded border shadow-sm p-4 border-l-4 border-orange-400">
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">31 - 60 Days Overdue</p>
            <p class="text-2xl font-black text-gray-800 mt-1">Rs. {{ number_format($aging['31_60']['amount'], 0) }}</p>
            <p class="text-sm text-gray-400 mt-1">{{ $aging['31_60']['count'] }} Invoices</p>
        </div>

        <div class="bg-white rounded border shadow-sm p-4 border-l-4 border-red-500">
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">61 - 90 Days Overdue</p>
            <p class="text-2xl font-black text-gray-800 mt-1">Rs. {{ number_format($aging['61_90']['amount'], 0) }}</p>
            <p class="text-sm text-gray-400 mt-1">{{ $aging['61_90']['count'] }} Invoices</p>
        </div>

        <div class="bg-white rounded border shadow-sm p-4 border-l-4 border-red-800">
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">90+ Days Overdue (Critical)</p>
            <p class="text-2xl font-black text-gray-800 mt-1">Rs. {{ number_format($aging['90_plus']['amount'], 0) }}</p>
            <p class="text-sm text-gray-400 mt-1">{{ $aging['90_plus']['count'] }} Invoices</p>
        </div>
    </div>

</div>
@endsection
