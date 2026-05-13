@extends('layouts.admin.layout')

@section('title', 'Ledger')

@section('content')

<div class="relative">

    @include('admin.finance.components.header', [
        'title' => 'Finance Ledger',
        'subtitle' => 'Track financial entries'
    ])

    @include('admin.finance.partials.tabs')

    <div class="bg-white custom-shadow border overflow-hidden mt-4">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Description</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-600">Debit</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-600">Credit</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-600">Balance</th>
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
