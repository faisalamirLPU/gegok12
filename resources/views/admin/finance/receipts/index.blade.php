@extends('layouts.admin.layout')

@section('title', 'Receipts')

@section('content')

<div class="relative">

    @include('admin.finance.components.header', [
        'title' => 'Receipts',
        'subtitle' => 'Receipt management system'
    ])

    @include('admin.finance.partials.tabs')

    <div class="bg-white custom-shadow border p-12 text-center text-gray-500">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h4 class="text-lg font-bold text-gray-800">
            Receipt Module Under Development
        </h4>
        <p class="text-sm text-gray-400 mt-2">
            This module is currently being implemented.
        </p>
    </div>

</div>

@endsection
