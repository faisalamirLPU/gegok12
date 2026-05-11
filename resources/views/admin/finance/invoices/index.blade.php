@extends('layouts.admin.layout')

@section('title', 'Invoices')

@section('content')

<div class="container-fluid">

    @include('admin.finance.components.header', [
        'title' => 'Invoices',
        'subtitle' => 'Manage student invoices'
    ])

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead class="table-light">

                        <tr>

                            <th>Invoice No</th>
                            <th>Student</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Due Date</th>

                        </tr>

                    </thead>

                    <tbody>

                        @include('admin.finance.partials.table-empty')

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection
