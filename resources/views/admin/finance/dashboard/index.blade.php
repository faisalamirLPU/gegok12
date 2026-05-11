@extends('layouts.admin.layout')

@section('title', 'Finance Dashboard')

@section('content')

<div class="container-fluid">

    @include('admin.finance.components.header', [
        'title' => 'Finance Dashboard',
        'subtitle' => 'School finance overview'
    ])

    <div class="row">

        <div class="col-md-3 mb-4">

            @include('admin.finance.components.stats-card', [
                'title' => 'Total Collection',
                'value' => '₹0'
            ])

        </div>

        <div class="col-md-3 mb-4">

            @include('admin.finance.components.stats-card', [
                'title' => 'Pending Fees',
                'value' => '₹0'
            ])

        </div>

        <div class="col-md-3 mb-4">

            @include('admin.finance.components.stats-card', [
                'title' => 'Overdue Invoices',
                'value' => '0'
            ])

        </div>

        <div class="col-md-3 mb-4">

            @include('admin.finance.components.stats-card', [
                'title' => 'Today Collection',
                'value' => '₹0'
            ])

        </div>

    </div>

</div>

@endsection
