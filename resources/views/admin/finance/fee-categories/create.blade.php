@extends('layouts.admin.layout')

@section('title', 'Create Fee Category')

@section('content')
<div class="relative">
    {{-- Page Header --}}
    <div class="flex flex-wrap lg:flex-row justify-between my-3">
        <div>
            <h1 class="admin-h1 my-3">Configure New Category</h1>
        </div>
        <div class="flex items-center">
            <a href="{{ route('finance.fee-categories.index') }}" class="no-underline text-gray-700 px-4 flex items-center bg-gray-100 border rounded py-1.5 justify-center hover:bg-gray-200 transition">
                <span class="text-xs font-semibold uppercase tracking-wide">Back to Categories</span>
            </a>
        </div>
    </div>

    {{-- Navigation --}}
    @include('admin.finance.partials.tabs')

    {{-- Form Container --}}
    <div class="mt-6">
        <form method="POST" action="{{ route('finance.fee-categories.store') }}"
              class="bg-white custom-shadow border overflow-hidden">
            @csrf
            <input type="hidden" name="school_id" value="{{ auth()->user()->school_id }}">
            <input type="hidden" name="academic_year_id" value="{{ \App\Helpers\SiteHelper::getAcademicYear(auth()->user()->school_id)->id }}">

            {{-- Form Body --}}
            <div class="p-6">
                <h2 class="text-sm font-semibold text-gray-800 uppercase tracking-wider mb-6 border-b pb-2">Category Identification & Billing</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @include('admin.finance.fee-categories.form')
                </div>
            </div>

            {{-- Form Footer --}}
            <div class="bg-gray-50 border-t p-6 flex items-center gap-3">
                <button type="submit" class="no-underline text-white px-10 flex items-center custom-green py-2 justify-center shadow-sm hover:shadow-md transition">
                    <span class="text-sm font-bold uppercase tracking-wider">Save Category</span>
                </button>
                <a href="{{ route('finance.fee-categories.index') }}" class="no-underline text-gray-700 px-8 flex items-center bg-gray-100 border py-2 justify-center rounded hover:bg-gray-200 transition">
                    <span class="text-sm font-semibold">Cancel</span>
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
