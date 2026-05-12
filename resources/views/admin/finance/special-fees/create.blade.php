@extends('layouts.admin.layout')

@section('title', 'Assign Special Fee')

@section('content')

    <div class="max-w-5xl mx-auto px-4 py-6">

        <div class="bg-white shadow-xl rounded-xl border border-gray-200 overflow-hidden">

            <div class="p-6 border-b bg-gray-50">

                <h1 class="text-2xl font-bold text-gray-800">
                    Assign Special Fee
                </h1>

            </div>

            <form method="POST" action="{{ route('finance.special-fees.store') }}"
                class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

                @csrf

                <div>

                    <label class="block mb-2 font-semibold text-gray-700">
                        Student
                    </label>

                    <select name="user_id" id="student_id" class="w-full border rounded-lg px-4 py-3">

                        @foreach ($students as $student)
                            <option value="{{ $student->id }}">

                            {{-- <option value="{{ $student->id }}"> --}}
                                {{ optional($student->userprofile)->firstname }}
                                {{ optional($student->userprofile)->lastname }}
                                -
                                {{ optional(optional($student->studentAcademicLatest)->standardLink->standard)->name }}
                                {{ optional(optional($student->studentAcademicLatest)->standardLink->section)->name }}
                            {{-- </option> --}}

                            </option>
                        @endforeach

                    </select>

                </div>

                <div>

                    <label class="block mb-2 font-semibold text-gray-700">
                        Fee Category
                    </label>

                    <select
    name="fee_category_id"  id="fee_category_id"
    class="w-full border rounded-lg px-4 py-3"
    required
>

    <option value="">
        Select Fee Category
    </option>

    @foreach($categories as $category)

        <option value="{{ $category->id }}">

            {{ $category->name }}

        </option>

    @endforeach

</select>

                </div>

                <div>

                    <label class="block mb-2 font-semibold text-gray-700">
                        Amount
                    </label>

                    <input
    type="number"
    step="0.01"
    name="amount"
    id="amount"
    class="w-full border border-gray-300 rounded-lg px-4 py-3"
>

                </div>

                <div>

                    <label class="block mb-2 font-semibold text-gray-700">
                        Due Date
                    </label>

                    <input type="date" name="due_date" class="w-full border rounded-lg px-4 py-3">

                </div>

                <div class="md:col-span-2">

                    <label class="block mb-2 font-semibold text-gray-700">
                        Remarks
                    </label>

                    <textarea name="remarks" rows="4" class="w-full border rounded-lg px-4 py-3"></textarea>

                </div>

                <div class="md:col-span-2">

                    <button type="submit"
                        class="bg-red-700 hover:bg-red-800 text-white px-6 py-3 rounded-lg font-semibold">

                        Assign Fee

                    </button>

                </div>

            </form>

        </div>

    </div>

    <script>

document.addEventListener('DOMContentLoaded', function () {

    const categorySelect =
        document.getElementById('fee_category_id');

    const amountInput =
        document.getElementById('amount');

    categorySelect.addEventListener(
        'change',
        function () {

            fetch(
                "{{ route('finance.special-fees.get-amount') }}",
                {
                    method: 'POST',

                    headers: {
                        'Content-Type': 'application/json',

                        'X-CSRF-TOKEN':
                            '{{ csrf_token() }}'
                    },

                    body: JSON.stringify({
                        fee_category_id: this.value
                    })
                }
            )

            .then(response => response.json())

            .then(data => {

                amountInput.value = data.amount;

            });

        }
    );

});

</script>

@endsection
