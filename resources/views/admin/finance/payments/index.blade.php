@extends('layouts.admin.layout')

@section('content')

<div class="container">

    <h2>Fee Invoices</h2>

    @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif

    <table class="table">

        <thead>
            <tr>
                <th>Invoice</th>
                <th>Student</th>
                <th>Total</th>
                <th>Paid</th>
                <th>Balance</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>

            @foreach($fees as $fee)

                <tr>

                    <td>
                        {{ $fee->invoice_no }}
                    </td>

                    <td>
                        {{ $fee->user_id }}
                    </td>

                    <td>
                        ₹{{ number_format($fee->total_amount, 2) }}
                    </td>

                    <td>
                        ₹{{ number_format($fee->paid_amount, 2) }}
                    </td>

                    <td>
                        ₹{{ number_format($fee->balance, 2) }}
                    </td>

                    <td>

                        @if($fee->status == 0)

                            Unpaid

                        @elseif($fee->status == 1)

                            Partial

                        @else

                            Paid

                        @endif

                    </td>

                    <td>

                        <a
                            href="{{ route('finance.payments.create', $fee->id) }}"
                            class="btn btn-primary btn-sm"
                        >
                            Collect Payment
                        </a>

                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>

    {{ $fees->links() }}

</div>

@endsection
