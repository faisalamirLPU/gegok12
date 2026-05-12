@extends('layouts.admin.layout')

@section('title', 'Create Fee Category')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="mb-8">

        <h1 class="text-3xl font-bold text-gray-800">
            Create Fee Category
        </h1>

        <p class="text-gray-600 mt-2">
            Add a new ERP finance category
        </p>

    </div>

    @include('admin.finance.partials.alerts')

    {{-- Form --}}
    <form method="POST"
          action="{{ route('finance.fee-categories.store') }}"
          class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">

        @csrf

        <input type="hidden"
               name="school_id"
               value="{{ auth()->user()->school_id }}">

        <input type="hidden"
               name="academic_year_id"
               value="{{ \App\Helpers\SiteHelper::getAcademicYear(auth()->user()->school_id)->id }}">


        {{-- Section --}}
        <div class="border-b border-gray-200 p-6 bg-gray-50">

            <h2 class="text-xl font-semibold text-gray-800 mb-6">
                Category Information
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                @include('admin.finance.fee-categories.form')

            </div>

        </div>


        {{-- Footer --}}
        <div class="bg-gray-50 border-t border-gray-200 p-6 flex items-center gap-4">

            <button type="submit"
                    class="bg-red-700 hover:bg-red-800 text-white px-6 py-3 rounded-lg font-semibold shadow transition">

                Save Category

            </button>

            <a href="{{ route('finance.fee-categories.index') }}"
               class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold transition">

                Cancel

            </a>

        </div>

    </form>

</div>

@endsection
