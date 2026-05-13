@extends('layouts.admin.layout')

@section('title', 'Invoices')

@section('content')

<div class="relative">

    @include('admin.finance.components.header', [
        'title' => 'Invoices',
        'subtitle' => 'Manage student invoices'
    ])

    @include('admin.finance.partials.tabs')

    <div class="bg-white custom-shadow border overflow-hidden mt-4">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Invoice No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Student</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-600">Total</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Due Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @include('admin.finance.partials.table-empty', ['colspan' => 5])
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
