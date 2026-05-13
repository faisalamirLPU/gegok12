@extends('layouts.admin.layout')

@section('title', 'Finance Reports')

@section('content')

<div class="relative">

    @include('admin.finance.components.header', [
        'title' => 'Finance Reports',
        'subtitle' => 'School finance analytics and reports'
    ])

    @include('admin.finance.partials.tabs')

    <div class="flex flex-wrap -mx-1 mt-4">
        <div class="w-full md:w-1/3 px-1 my-2">
            @include('admin.finance.components.stats-card', [
                'title' => 'Monthly Collection',
                'value' => '₹0'
            ])
        </div>

        <div class="w-full md:w-1/3 px-1 my-2">
            @include('admin.finance.components.stats-card', [
                'title' => 'Pending Amount',
                'value' => '₹0'
            ])
        </div>

        <div class="w-full md:w-1/3 px-1 my-2">
            @include('admin.finance.components.stats-card', [
                'title' => 'Overdue Amount',
                'value' => '₹0'
            ])
        </div>
    </div>
</div>

@endsection
