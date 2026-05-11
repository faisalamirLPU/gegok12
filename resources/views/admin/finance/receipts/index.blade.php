@extends('layouts.admin.layout')

@section('title', 'Receipts')

@section('content')

<div class="container-fluid">

    @include('admin.finance.components.header', [
        'title' => 'Receipts',
        'subtitle' => 'Receipt management system'
    ])

    <div class="card border-0 shadow-sm">

        <div class="card-body text-center py-5">

            <h4 class="text-muted">
                Receipt Module Under Development
            </h4>

        </div>

    </div>

</div>

@endsection
