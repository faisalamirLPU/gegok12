@extends('layouts.admin.layout')

@section('title', 'Assign Special Fee')

@section('content')

<div class="relative">

    {{-- Page Header --}}
    <div class="flex flex-wrap lg:flex-row justify-between my-3">
        <div>
            <h1 class="admin-h1 my-3">Assign Special Fee</h1>
        </div>

        <div class="flex items-center">
            <a href="{{ route('finance.special-fees.index') }}"
               class="no-underline text-gray-700 px-4 flex items-center bg-gray-100 border rounded py-1.5 justify-center hover:bg-gray-200 transition">

                <span class="text-xs font-semibold uppercase tracking-wide">
                    Back to Special Fees
                </span>
            </a>
        </div>
    </div>

    {{-- Navigation --}}
    @include('admin.finance.partials.tabs')

    {{-- Form Container --}}
    <div class="mt-6">

        <form method="POST"
              action="{{ route('finance.special-fees.store') }}"
              class="bg-white custom-shadow border overflow-hidden">

            @csrf

            {{-- Hidden Inputs --}}
            <input type="hidden"
                   name="school_id"
                   value="{{ auth()->user()->school_id }}">

            <input type="hidden"
                   name="academic_year_id"
                   value="{{ \App\Helpers\SiteHelper::getAcademicYear(auth()->user()->school_id)->id }}">

            {{-- Form Body --}}
            <div class="p-6">

                <h2 class="text-sm font-semibold text-gray-800 uppercase tracking-wider mb-6 border-b pb-2">
                    Student & Fee Information
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Student Selection --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">
                            Select Student
                            <span class="text-red-500">*</span>
                        </label>

                        <select name="user_id"
                                id="student_id"
                                required
                                class="w-full border border-gray-300 rounded px-4 py-2 text-sm focus:border-blue-500 focus:ring-0">

                            <option value="">Choose a student...</option>

                            @foreach ($students as $student)

                                <option value="{{ $student->id }}">

                                    {{ optional($student->userprofile)->firstname }}
                                    {{ optional($student->userprofile)->lastname }}

                                    -

                                    {{ optional(optional($student->studentAcademicLatest)->standardLink->standard)->name }}

                                    {{ optional(optional($student->studentAcademicLatest)->standardLink->section)->name }}

                                </option>

                            @endforeach

                        </select>
                    </div>

                    {{-- Fee Category --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">
                            Fee Category
                            <span class="text-red-500">*</span>
                        </label>

                        <select name="fee_category_id"
                                id="fee_category_id"
                                required
                                class="w-full border border-gray-300 rounded px-4 py-2 text-sm focus:border-blue-500 focus:ring-0">

                            <option value="">Choose a category...</option>

                            @foreach($categories as $category)

                                <option value="{{ $category->id }}">
                                    {{ $category->name }}
                                </option>

                            @endforeach

                        </select>
                    </div>

                    {{-- Fee Amount --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">
                            Fee Amount (Rs.)
                            <span class="text-red-500">*</span>
                        </label>

                        <div class="relative">

                            <span class="absolute left-3 top-2 text-gray-400 text-sm">
                                Rs.
                            </span>

                            <input type="number"
                                   step="0.01"
                                   name="amount"
                                   id="amount"
                                   required
                                   placeholder="0.00"
                                   class="w-full border border-gray-300 rounded pl-10 pr-4 py-2 text-sm font-bold text-gray-800 focus:border-blue-500 focus:ring-0">

                        </div>
                    </div>

                    {{-- Due Date --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">
                            Due Date
                        </label>

                        <input type="date"
                               name="due_date"
                               class="w-full border border-gray-300 rounded px-4 py-2 text-sm focus:border-blue-500 focus:ring-0">
                    </div>

                    {{-- Remarks --}}
                    <div class="md:col-span-2">

                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">
                            Administrative Remarks
                        </label>

                        <textarea name="remarks"
                                  rows="4"
                                  placeholder="Reason for assigning special fee (Fine, Damage Charge, Late Admission, etc.)"
                                  class="w-full border border-gray-300 rounded px-4 py-2 text-sm focus:border-blue-500 focus:ring-0"></textarea>

                    </div>

                </div>
            </div>

            {{-- Footer --}}
            <div class="bg-gray-50 border-t p-6 flex items-center gap-3">

                <button type="submit"
                        class="no-underline text-white px-10 flex items-center custom-green py-2 justify-center shadow-sm hover:shadow-md transition">

                    <span class="text-sm font-bold uppercase tracking-wider">
                        Assign Special Fee
                    </span>

                </button>

                <a href="{{ route('finance.special-fees.index') }}"
                   class="no-underline text-gray-700 px-8 flex items-center bg-gray-100 border py-2 justify-center rounded hover:bg-gray-200 transition">

                    <span class="text-sm font-semibold">
                        Cancel
                    </span>

                </a>

            </div>

        </form>

    </div>

</div>

{{-- Auto Fetch Category Amount --}}
<script>

document.addEventListener('DOMContentLoaded', function () {

    const categorySelect = document.getElementById('fee_category_id');
    const amountInput = document.getElementById('amount');

    categorySelect.addEventListener('change', function () {

        if (!this.value) return;

        fetch("{{ route('finance.special-fees.get-amount') }}", {

            method: 'POST',

            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },

            body: JSON.stringify({
                fee_category_id: this.value
            })

        })

        .then(response => response.json())

        .then(data => {

            amountInput.value = data.amount;

        });

    });

});

</script>

@endsection