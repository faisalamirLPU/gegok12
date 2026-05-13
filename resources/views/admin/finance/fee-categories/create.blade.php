@extends('layouts.admin.layout')

@section('title', 'Create Fee Category')

@section('content')

<div class="relative">
    {{-- Header --}}
    <div class="flex flex-wrap lg:flex-row justify-between my-3">
        <div>
            <h1 class="admin-h1 my-3">Create Fee Category</h1>
        </div>
        <div class="flex items-center">
            <a href="{{ route('finance.fee-categories.index') }}" class="no-underline text-gray-600 px-4 flex items-center bg-gray-100 border rounded py-1.5 justify-center hover:bg-gray-200 transition">
                <span class="text-[10px] font-bold uppercase tracking-wider">Back to Categories</span>
            </a>
        </div>
    </div>

    @include('admin.finance.partials.tabs')

    {{-- Form --}}
    <form method="POST" action="{{ route('finance.fee-categories.store') }}"
          class="bg-white custom-shadow border overflow-hidden">
        @csrf
        <input type="hidden" name="school_id" value="{{ auth()->user()->school_id }}">
        <input type="hidden" name="academic_year_id" value="{{ \App\Helpers\SiteHelper::getAcademicYear(auth()->user()->school_id)->id }}">

        {{-- Section --}}
        <div class="p-6">
            <h2 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-6 border-b pb-1">Category Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @include('admin.finance.fee-categories.form')
            </div>
        </div>

        {{-- Footer --}}
        <div class="bg-gray-50 border-t border-gray-100 p-6 flex items-center gap-4">
            <button type="submit" class="no-underline text-white px-8 flex items-center custom-green py-2 justify-center">
                <span class="text-[10px] font-bold uppercase tracking-wider">Save Category</span>
            </button>
            <a href="{{ route('finance.fee-categories.index') }}" class="no-underline text-gray-600 px-8 flex items-center bg-gray-100 border rounded py-2 justify-center hover:bg-gray-200 transition">
                <span class="text-[10px] font-bold uppercase tracking-wider">Cancel</span>
            </a>
        </div>
    </form>
</div>

@endsection
