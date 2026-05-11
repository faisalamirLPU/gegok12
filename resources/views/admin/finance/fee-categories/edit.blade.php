@extends('layouts.admin.layout')

@section('title', 'Edit Fee Category')

@section('content')

<div class="container-fluid">

    @include('admin.finance.partials.alerts')

    @include('admin.finance.components.breadcrumb', [
        'items' => ['Finance', 'Fee Categories', 'Edit']
    ])

    @include('admin.finance.components.header', [
        'title' => 'Edit Fee Category',
        'subtitle' => 'Update ERP finance category'
    ])

    <div class="card border-0 shadow-sm">

        <div class="card-body p-4">

            <form method="POST"
                  action="{{ route('finance.fee-categories.update', $feeCategory->id) }}">

                @csrf
                @method('PUT')

                @include('admin.finance.fee-categories.form')

                <div class="border-top pt-4 d-flex gap-2">

                    <button type="submit"
                            class="btn btn-primary">

                        <i class="fas fa-save me-1"></i>

                        Update Category

                    </button>

                    <a href="{{ route('finance.fee-categories.index') }}"
                       class="btn btn-light border">

                        Cancel

                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection
