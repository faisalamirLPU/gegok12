@extends('layouts.admin.layout')

@section('title', 'Payments')

@section('content')

<div class="container-fluid">

    @include('admin.finance.components.header', [
        'title' => 'Payments',
        'subtitle' => 'Manage finance collections'
    ])

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead class="table-light">

                        <tr>

                            <th>Receipt No</th>
                            <th>Student</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Date</th>

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
