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
        action="{{ route('admin.exams.store') }}"
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

                    <div class="flex items-center gap-2">
                        <select
                            id="examNameSelect"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-red-600 focus:outline-none"
                        >
                            <option value="">-- Select Exam Name --</option>
                            @foreach($examNames as $examName)
                                <option value="{{ $examName }}" {{ old('name') == $examName ? 'selected' : '' }}>
                                    {{ $examName }}
                                </option>
                            @endforeach
                            <option value="__add_new" {{ old('name_custom') ? 'selected' : '' }}>
                                Add new exam name/type
                            </option>
                        </select>

                        <button
                            type="button"
                            id="toggleExamNameInput"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg text-sm font-semibold transition"
                        >
                            Add New
                        </button>
                    </div>

                    <input
                        type="hidden"
                        id="examNameHidden"
                        name="name"
                        value="{{ old('name') }}"
                    >

                    <input
                        type="text"
                        id="examNameCustom"
                        name="name_custom"
                        value="{{ old('name_custom') }}"
                        placeholder="Enter new exam name or type"
                        class="w-full mt-3 rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-red-600 focus:outline-none {{ old('name_custom') ? '' : 'hidden' }}"
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
                                <select
                                    name="subjects[0][name]"
                                    class="subject-select w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-600 focus:outline-none"
                                    required
                                >
                                    <option value="">-- Select Subject --</option>
                                </select>
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

const subjectsByClass = @json($subjectsByClass);

document.addEventListener('DOMContentLoaded', function () {

    const classSelect = document.querySelector('select[name="standard_link_id"]');
    const examNameSelect = document.getElementById('examNameSelect');
    const examNameHidden = document.getElementById('examNameHidden');
    const examNameCustom = document.getElementById('examNameCustom');
    const toggleExamNameInput = document.getElementById('toggleExamNameInput');
    const subjectsTable = document.getElementById('subjectsTable');
    const addSubjectButton = document.getElementById('addSubject');
    let subjectIndex = document.querySelectorAll('.subject-row').length;

    const buildSubjectOptions = function (classId) {
        const subjects = subjectsByClass[classId] || [];

        if (subjects.length === 0) {
            return '<option value="">No subjects found for this class</option>';
        }

        return ['<option value="">-- Select Subject --</option>']
            .concat(subjects.map(subject => {
                return `<option value="${subject.name}">${subject.name}</option>`;
            }))
            .join('');
    };

    const refreshSubjectSelects = function () {
        const currentClassId = classSelect.value;
        document.querySelectorAll('.subject-select').forEach(select => {
            select.innerHTML = buildSubjectOptions(currentClassId);
        });
    };

    const updateExamNameValue = function () {
        if (examNameSelect.value === '__add_new') {
            examNameCustom.classList.remove('hidden');
            examNameHidden.value = examNameCustom.value.trim();
        } else {
            examNameCustom.classList.add('hidden');
            examNameHidden.value = examNameSelect.value;
        }
    };

    classSelect.addEventListener('change', function () {
        refreshSubjectSelects();
    });

    examNameSelect.addEventListener('change', function () {
        updateExamNameValue();
    });

    toggleExamNameInput.addEventListener('click', function () {
        examNameSelect.value = '__add_new';
        examNameCustom.classList.remove('hidden');
        examNameHidden.value = examNameCustom.value.trim();
        examNameCustom.focus();
    });

    examNameCustom.addEventListener('input', function () {
        examNameHidden.value = this.value.trim();
    });

    const addSubjectRow = function () {
        const row = document.createElement('tr');

        row.className = 'subject-row border-b border-gray-200';

        row.innerHTML = `
            <td class="px-4 py-4">
                <select
                    name="subjects[${subjectIndex}][name]"
                    class="subject-select w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-600 focus:outline-none"
                    required
                >
                    ${buildSubjectOptions(classSelect.value)}
                </select>
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
    };

    addSubjectButton.addEventListener('click', function () {
        addSubjectRow();
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-subject')) {
            e.target.closest('tr').remove();
        }
    });

    refreshSubjectSelects();
    updateExamNameValue();
});
</script>

@endsection
