@extends('layouts.admin.layout')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-6">

    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">
            Create Examination
        </h1>

        <p class="text-gray-600 mt-2">
            Create subject-wise examination with complete ERP workflow
        </p>
    </div>

    @include('partials.message')

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-6">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form -->
    <form
        method="POST"
        action="{{ route('core.exams.store') }}"
        class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden"
    >
        @csrf

        <!-- Basic Info -->
        <div class="border-b border-gray-200 p-6 bg-gray-50">

            <h2 class="text-xl font-semibold text-gray-800 mb-6">
                Basic Information
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Exam Name -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Exam Name *
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Mid Term Examination"
                        required
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-red-600 focus:outline-none"
                    >
                </div>

                <!-- Class -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Class / Section *
                    </label>

                    <select
                        name="standard_link_id"
                        required
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-red-600 focus:outline-none"
                    >
                        <option value="">
                            -- Select Class --
                        </option>

                        @foreach($classes as $class)
                            <option
                                value="{{ $class->id }}"
                                {{ old('standard_link_id') == $class->id ? 'selected' : '' }}
                            >
                                {{ $class->StandardSection }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Exam Date -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Exam Date
                    </label>

                    <input
                        type="date"
                        name="exam_date"
                        value="{{ old('exam_date') }}"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-red-600 focus:outline-none"
                    >
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Exam Status
                    </label>

                    <select
                        name="status"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-red-600 focus:outline-none"
                    >
                        <option value="draft">
                            Draft
                        </option>

                        <option value="scheduled">
                            Scheduled
                        </option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Subjects -->
        <div class="p-6">

            <div class="flex items-center justify-between mb-6">

                <div>
                    <h2 class="text-xl font-semibold text-gray-800">
                        Subjects Configuration
                    </h2>

                    <p class="text-sm text-gray-600 mt-1">
                        Configure subject-wise marks and passing criteria
                    </p>
                </div>

                <button
                    type="button"
                    id="addSubject"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition"
                >
                    + Add Subject
                </button>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto border border-gray-200 rounded-xl">

                <table class="min-w-full">

                    <thead class="bg-gray-100 border-b border-gray-200">

                        <tr>

                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">
                                Subject Name
                            </th>

                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">
                                Maximum Marks
                            </th>

                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">
                                Passing Marks
                            </th>

                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">
                                Subject Exam Date
                            </th>

                            <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">
                                Action
                            </th>

                        </tr>

                    </thead>

                    <tbody id="subjectsTable">

                        <!-- Default Row -->
                        <tr class="subject-row border-b border-gray-200">

                            <!-- Subject Name -->
                            <td class="px-4 py-4">
                                <input
                                    type="text"
                                    name="subjects[0][name]"
                                    placeholder="Mathematics"
                                    required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-600 focus:outline-none"
                                >
                            </td>

                            <!-- Max Marks -->
                            <td class="px-4 py-4">
                                <input
                                    type="number"
                                    step="0.01"
                                    name="subjects[0][max_marks]"
                                    value="100"
                                    required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-600 focus:outline-none"
                                >
                            </td>

                            <!-- Pass Marks -->
                            <td class="px-4 py-4">
                                <input
                                    type="number"
                                    step="0.01"
                                    name="subjects[0][pass_marks]"
                                    value="35"
                                    required
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-600 focus:outline-none"
                                >
                            </td>

                            <!-- Exam Date -->
                            <td class="px-4 py-4">
                                <input
                                    type="date"
                                    name="subjects[0][exam_date]"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-600 focus:outline-none"
                                >
                            </td>

                            <!-- Remove -->
                            <td class="px-4 py-4 text-center">
                                <button
                                    type="button"
                                    class="remove-subject bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-sm"
                                    style="display:none;"
                                >
                                    Remove
                                </button>
                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

        <!-- Footer -->
        <div class="bg-gray-50 border-t border-gray-200 p-6 flex items-center gap-4">

            <button
                type="submit"
                class="bg-red-700 hover:bg-red-800 text-white px-6 py-3 rounded-lg font-semibold shadow transition"
            >
                Create Examination
            </button>

            <a
                href="{{ url('/admin/exams') }}"
                class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold transition"
            >
                Cancel
            </a>

        </div>

    </form>

</div>

<!-- Script -->
<script>

document.addEventListener('DOMContentLoaded', function () {

    let subjectIndex = 1;

    const subjectsTable =
        document.getElementById('subjectsTable');

    const addSubjectButton =
        document.getElementById('addSubject');

    /*
    |--------------------------------------------------------------------------
    | Add Subject Row
    |--------------------------------------------------------------------------
    */

    addSubjectButton.addEventListener('click', function () {

        const row = document.createElement('tr');

        row.className =
            'subject-row border-b border-gray-200';

        row.innerHTML = `

            <td class="px-4 py-4">
                <input
                    type="text"
                    name="subjects[${subjectIndex}][name]"
                    placeholder="Subject Name"
                    required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-600 focus:outline-none"
                >
            </td>

            <td class="px-4 py-4">
                <input
                    type="number"
                    step="0.01"
                    name="subjects[${subjectIndex}][max_marks]"
                    value="100"
                    required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-600 focus:outline-none"
                >
            </td>

            <td class="px-4 py-4">
                <input
                    type="number"
                    step="0.01"
                    name="subjects[${subjectIndex}][pass_marks]"
                    value="35"
                    required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-600 focus:outline-none"
                >
            </td>

            <td class="px-4 py-4">
                <input
                    type="date"
                    name="subjects[${subjectIndex}][exam_date]"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-600 focus:outline-none"
                >
            </td>

            <td class="px-4 py-4 text-center">
                <button
                    type="button"
                    class="remove-subject bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-sm"
                >
                    Remove
                </button>
            </td>

        `;

        subjectsTable.appendChild(row);

        subjectIndex++;
    });

    /*
    |--------------------------------------------------------------------------
    | Remove Subject
    |--------------------------------------------------------------------------
    */

    document.addEventListener('click', function (e) {

        if (e.target.classList.contains('remove-subject')) {

            e.target.closest('tr').remove();
        }
    });

});
</script>

@endsection
