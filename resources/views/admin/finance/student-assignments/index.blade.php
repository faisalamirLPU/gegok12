@extends('layouts.admin.layout')

@section('title', 'Student Assignments')

@section('content')

<div class="container-fluid">

    @include('admin.finance.components.header', [
        'title' => 'Student Fee Assignments',
        'subtitle' => 'Assign fee structures to students'
    ])

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead class="table-light">

                        <tr>

                            <th>Student</th>
                            <th>Class</th>
                            <th>Structure</th>
                            <th>Status</th>

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
