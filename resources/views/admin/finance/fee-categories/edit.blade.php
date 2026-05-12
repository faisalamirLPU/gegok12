@extends('layouts.admin.layout')

@section('title', 'Edit Fee Category')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="mb-8">

        <h1 class="text-3xl font-bold text-gray-800">
            Edit Fee Category
        </h1>

        <p class="text-gray-600 mt-2">
            Update ERP finance category
        </p>

    </div>

    {{-- Alerts --}}
    @include('admin.finance.partials.alerts')


    {{-- Form --}}
    <form method="POST"
          action="{{ route('finance.fee-categories.update', $feeCategory->id) }}"
          class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">

        @csrf
        @method('PUT')


        {{-- Hidden Fields --}}
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

                {{-- Category Name --}}
                <div>

                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Category Name
                    </label>

                    <input type="text"
                           name="name"
                           value="{{ old('name', $feeCategory->name ?? '') }}"
                           placeholder="Enter category name"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-600 focus:outline-none">

                </div>


                {{-- Category Code --}}
                <div>

                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Category Code
                    </label>

                    <input type="text"
                           name="code"
                           value="{{ old('code', $feeCategory->code ?? '') }}"
                           placeholder="Example: TUITION_FEE"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-600 focus:outline-none">

                </div>


                {{-- Frequency --}}
                <div>

                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Frequency
                    </label>

                    <select name="frequency"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-600 focus:outline-none">

                        <option value="">
                            Select Frequency
                        </option>

                        <option value="monthly"
                            {{ old('frequency', $feeCategory->frequency) == 'monthly' ? 'selected' : '' }}>
                            Monthly
                        </option>

                        <option value="quarterly"
                            {{ old('frequency', $feeCategory->frequency) == 'quarterly' ? 'selected' : '' }}>
                            Quarterly
                        </option>

                        <option value="yearly"
                            {{ old('frequency', $feeCategory->frequency) == 'yearly' ? 'selected' : '' }}>
                            Yearly
                        </option>

                        <option value="one_time"
                            {{ old('frequency', $feeCategory->frequency) == 'one_time' ? 'selected' : '' }}>
                            One Time
                        </option>

                    </select>

                </div>


                {{-- Status --}}
                <div>

                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Status
                    </label>

                    <select name="status"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-600 focus:outline-none">

                        <option value="1"
                            {{ old('status', $feeCategory->status) == 1 ? 'selected' : '' }}>
                            Active
                        </option>

                        <option value="0"
                            {{ old('status', $feeCategory->status) == 0 ? 'selected' : '' }}>
                            Inactive
                        </option>

                    </select>

                </div>


                {{-- Refundable --}}
                <div class="md:col-span-2">

                    <div class="border border-gray-300 rounded-lg p-4">

                        <label class="flex items-start gap-3">

                            <input type="checkbox"
                                   name="is_refundable"
                                   value="1"
                                   {{ old('is_refundable', $feeCategory->is_refundable) ? 'checked' : '' }}
                                   class="mt-1 w-5 h-5 rounded border-gray-300 text-red-600 focus:ring-red-500">

                            <div>

                                <div class="font-medium text-gray-700">
                                    Refundable Fee
                                </div>

                                <div class="text-sm text-gray-500 mt-1">
                                    Students can request refunds for this category.
                                </div>

                            </div>

                        </label>

                    </div>

                </div>


                {{-- Optional --}}
                <div class="md:col-span-2">

                    <div class="border border-gray-300 rounded-lg p-4">

                        <label class="flex items-start gap-3">

                            <input type="checkbox"
                                   name="is_optional"
                                   value="1"
                                   {{ old('is_optional', $feeCategory->is_optional) ? 'checked' : '' }}
                                   class="mt-1 w-5 h-5 rounded border-gray-300 text-red-600 focus:ring-red-500">

                            <div>

                                <div class="font-medium text-gray-700">
                                    Optional Fee
                                </div>

                                <div class="text-sm text-gray-500 mt-1">
                                    Students may choose whether to pay this fee.
                                </div>

                            </div>

                        </label>

                    </div>

                </div>


                {{-- Description --}}
                <div class="md:col-span-2">

                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Description
                    </label>

                    <textarea name="description"
                              rows="5"
                              placeholder="Enter category description..."
                              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-600 focus:outline-none">{{ old('description', $feeCategory->description ?? '') }}</textarea>

                </div>

            </div>

        </div>


        {{-- Footer --}}
        <div class="bg-gray-50 border-t border-gray-200 p-6 flex items-center gap-4">

            <button type="submit"
                    class="bg-red-700 hover:bg-red-800 text-white px-6 py-3 rounded-lg font-semibold shadow transition">

                Update Category

            </button>

            <a href="{{ route('finance.fee-categories.index') }}"
               class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold transition">

                Cancel

            </a>

        </div>

    </form>

</div>

@endsection
