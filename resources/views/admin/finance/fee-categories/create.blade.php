@extends('layouts.admin.layout')

@section('title', 'Create Fee Category')

@section('content')

<div class="container-fluid">

    @include('admin.finance.partials.alerts')

    @include('admin.finance.components.breadcrumb', [
        'items' => ['Finance', 'Fee Categories', 'Create']
    ])

    @include('admin.finance.components.header', [
        'title' => 'Create Fee Category',
        'subtitle' => 'Add a new ERP finance category'
    ])

    <div class="card border-0 shadow-sm">

        <div class="card-body p-4">

            <form method="POST"
                  action="{{ route('finance.fee-categories.store') }}">

                @csrf

                <input type="hidden"
                       name="school_id"
                       value="{{ auth()->user()->school_id }}">

                <input type="hidden"
                       name="academic_year_id"
                       value="{{ \App\Helpers\SiteHelper::getAcademicYear(auth()->user()->school_id)->id }}">

                @include('admin.finance.fee-categories.form')

                <div class="border-top pt-4 d-flex gap-2">

                    <button type="submit"
                            class="btn btn-primary">

                        <i class="fas fa-save me-1"></i>

                        Save Category

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
