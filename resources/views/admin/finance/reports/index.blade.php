@extends('layouts.admin.layout')

@section('title', 'Finance Reports')

@section('content')

<div class="container-fluid">

    @include('admin.finance.components.header', [
        'title' => 'Finance Reports',
        'subtitle' => 'School finance analytics and reports'
    ])

    <div class="row">

        <div class="col-md-4 mb-4">

            @include('admin.finance.components.stats-card', [
                'title' => 'Monthly Collection',
                'value' => '₹0'
            ])

        </div>

        <div class="col-md-4 mb-4">

            @include('admin.finance.components.stats-card', [
                'title' => 'Pending Amount',
                'value' => '₹0'
            ])

        </div>

        <div class="col-md-4 mb-4">

            @include('admin.finance.components.stats-card', [
                'title' => 'Overdue Amount',
                'value' => '₹0'
            ])

        </div>

    </div>

</div>

@endsection
