@extends('layouts.admin.layout')

@section('title', 'Fee Structures')

@section('content')

<div class="container-fluid">

    @include('admin.finance.partials.alerts')

    @include('admin.finance.components.header', [
        'title' => 'Fee Structures',
        'subtitle' => 'Manage class-wise fee structures',
        'actions' => '
            <a href="'.route('finance.fee-structures.create').'"
               class="btn btn-primary">
                Add Fee Structure
            </a>
        '
    ])

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead class="table-light">

                        <tr>

                            <th>Title</th>
                            <th>Class</th>
                            <th>Installment</th>
                            <th>Status</th>
                            <th width="120">Action</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($structures ?? [] as $structure)

                            <tr>

                                <td>{{ $structure->title }}</td>

                                <td>{{ $structure->class->name ?? '-' }}</td>

                                <td>{{ ucfirst($structure->installment_type) }}</td>

                                <td>

                                    @if($structure->status)

                                        <span class="badge bg-success">
                                            Active
                                        </span>

                                    @else

                                        <span class="badge bg-danger">
                                            Inactive
                                        </span>

                                    @endif

                                </td>

                                <td>

                                    <a href="#"
                                       class="btn btn-warning btn-sm">

                                        Edit

                                    </a>

                                </td>

                            </tr>

                        @empty

                            @include('admin.finance.partials.table-empty')

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection
