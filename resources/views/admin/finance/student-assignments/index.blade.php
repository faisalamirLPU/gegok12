@extends('layouts.admin.layout')

@section('title', 'Student Fee Assignments')

@section('content')

<div class="container-fluid">

    @include('admin.finance.components.header', [
        'title' => 'Student Fee Assignments',
        'subtitle' => 'Manage assigned student fee structures'
    ])

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead class="table-light">

                        <tr>

                            <th>Student</th>

                            <th>Fee Structure</th>

                            <th>Assigned Amount</th>

                            <th>Paid</th>

                            <th>Balance</th>

                            <th>Due Date</th>

                            <th>Status</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($assignments as $assignment)

                            <tr>

                                <td>
                                    {{ $assignment->student->name ?? '-' }}
                                </td>

                                <td>
                                    {{ $assignment->feeStructure->title ?? '-' }}
                                </td>

                                <td>
                                    ₹{{ number_format($assignment->assigned_amount, 2) }}
                                </td>

                                <td>
                                    ₹{{ number_format($assignment->paid_amount, 2) }}
                                </td>

                                <td>
                                    ₹{{ number_format($assignment->balance, 2) }}
                                </td>

                                <td>
                                    {{ optional($assignment->due_date)->format('d M Y') }}
                                </td>

                                <td>

                                    @if($assignment->status == 1)

                                        <span class="badge bg-success">
                                            Paid
                                        </span>

                                    @else

                                        <span class="badge bg-warning">
                                            Pending
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            @include('admin.finance.partials.table-empty')

                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="mt-3">

                {{ $assignments->links() }}

            </div>

        </div>

    </div>

</div>

@endsection
