@extends('layouts.admin.layout')

@section('title', 'Create Fee Structure')

@section('content')

<div class="container-fluid">

    @include('admin.finance.partials.alerts')

    @include('admin.finance.components.header', [
        'title' => 'Create Fee Structure',
        'subtitle' => 'Build class-wise fee structures'
    ])

    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <form method="POST"
                  action="{{ route('finance.fee-structures.store') }}">

                @csrf

                @include('admin.finance.fee-structures.form')

                <button type="submit"
                        class="btn btn-primary">

                    Save Structure

                </button>

            </form>

        </div>

    </div>

</div>

@endsection
