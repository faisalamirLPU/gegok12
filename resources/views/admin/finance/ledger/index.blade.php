@extends('layouts.admin.layout')

@section('title', 'Ledger')

@section('content')

<div class="container-fluid">

    @include('admin.finance.components.header', [
        'title' => 'Finance Ledger',
        'subtitle' => 'Track financial entries'
    ])

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead class="table-light">

                        <tr>

                            <th>Date</th>
                            <th>Description</th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Balance</th>

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
