@extends('layouts.admin.layout')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-6">

    <!-- Header -->
    <div class="mb-8">

        <div class="flex items-center justify-between">

            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    Marks Entry
                </h1>

                <p class="text-gray-600 mt-2">
                    Enter subject-wise marks
                </p>
            </div>

            <a
                href="{{ url('/admin/exams/' . $exam->id) }}"
                class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-3 rounded-lg font-semibold"
            >
                Back
            </a>

        </div>
    </div>

    @include('partials.message')

    <!-- Exam Info -->
    <div class="bg-white rounded-xl shadow border border-gray-200 mb-6">

        <div class="p-6 border-b bg-gray-50">

            <h2 class="text-xl font-semibold text-gray-800">
                Examination Information
            </h2>

        </div>

        <div class="p-6">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

                <div>
                    <p class="text-sm text-gray-500">
                        Examination
                    </p>

                    <p class="font-semibold text-gray-800 mt-1">
                        {{ $exam->name }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">
                        Subject
                    </p>

                    <p class="font-semibold text-gray-800 mt-1">
                        {{ $subject->subject_name }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">
                        Maximum Marks
                    </p>

                    <p class="font-semibold text-gray-800 mt-1">
                        {{ $subject->max_marks }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">
                        Pass Marks
                    </p>

                    <p class="font-semibold text-gray-800 mt-1">
                        {{ $subject->pass_marks }}
                    </p>
                </div>

            </div>

        </div>

    </div>

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

    <!-- Marks Form -->
    <form
        method="POST"
        action="{{ route('admin.exams.save-marks', [
    'exam' => $exam->id,
    'subject' => $subject->id
]) }}"
    >

        @csrf

        <div class="bg-white rounded-xl shadow border border-gray-200 overflow-hidden">

            <div class="overflow-x-auto">

                <table class="min-w-full">

                    <thead class="bg-gray-100 border-b border-gray-200">

                        <tr>

                            <th class="px-4 py-4 text-left text-sm font-semibold text-gray-700">
                                Roll No
                            </th>

                            <th class="px-4 py-4 text-left text-sm font-semibold text-gray-700">
                                Student Name
                            </th>

                            <th class="px-4 py-4 text-left text-sm font-semibold text-gray-700">
                                Attendance
                            </th>

                            <th class="px-4 py-4 text-left text-sm font-semibold text-gray-700">
                                Marks
                            </th>

                            <th class="px-4 py-4 text-left text-sm font-semibold text-gray-700">
                                Grade
                            </th>

                            <th class="px-4 py-4 text-left text-sm font-semibold text-gray-700">
                                Status
                            </th>

                            <th class="px-4 py-4 text-left text-sm font-semibold text-gray-700">
                                Remarks
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($students as $index => $student)

                            @php
                                $mark = $marks[$student->id] ?? null;
                            @endphp

                            <tr class="border-b border-gray-100 hover:bg-gray-50">

                                <!-- Hidden Student ID -->
                                <input
                                    type="hidden"
                                    name="marks[{{ $index }}][student_id]"
                                    value="{{ $student->id }}"
                                >

                                <!-- Roll -->
                                <td class="px-4 py-4">

                                    <span class="font-medium text-gray-700">
                                        {{ $student->registration_number ?? 'N/A' }}
                                    </span>

                                </td>

                                <!-- Student -->
                                <td class="px-4 py-4">

                                    <div class="flex items-center gap-3">

                                        <img
                                            src="{{ asset(optional($student->userprofile)->avatar ?? 'uploads/default.png') }}"
                                            class="w-10 h-10 rounded-full object-cover border"
                                        >

                                        <div>

                                            <p class="font-semibold text-gray-800">
                                                {{ optional($student->userprofile)->firstname }}
                                                {{ optional($student->userprofile)->lastname }}
                                            </p>

                                        </div>

                                    </div>

                                </td>

                                <!-- Attendance -->
                                <td class="px-4 py-4">

                                    <select
                                        name="marks[{{ $index }}][attendance_status]"
                                        class="attendance-select w-full border border-gray-300 rounded-lg px-3 py-2"
                                    >
                                        <option
                                            value="present"
                                            {{ optional($mark)->attendance_status == 'present' ? 'selected' : '' }}
                                        >
                                            Present
                                        </option>

                                        <option
                                            value="absent"
                                            {{ optional($mark)->attendance_status == 'absent' ? 'selected' : '' }}
                                        >
                                            Absent
                                        </option>

                                        <option
                                            value="medical"
                                            {{ optional($mark)->attendance_status == 'medical' ? 'selected' : '' }}
                                        >
                                            Medical
                                        </option>

                                    </select>

                                </td>

                                <!-- Marks -->
                                <td class="px-4 py-4">

                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        max="{{ $subject->max_marks }}"
                                        name="marks[{{ $index }}][marks_obtained]"
                                        value="{{ optional($mark)->marks_obtained }}"
                                        class="marks-input w-full border border-gray-300 rounded-lg px-3 py-2"
                                    >

                                </td>

                                <!-- Grade -->
                                <td class="px-4 py-4">

                                    <span class="grade-display font-semibold text-blue-700">
                                        {{ optional($mark)->grade ?? '-' }}
                                    </span>

                                </td>

                                <!-- Pass/Fail -->
                                <td class="px-4 py-4">

                                    @if(optional($mark)->is_passed)

                                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-semibold">
                                            Pass
                                        </span>

                                    @elseif($mark)

                                        <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-semibold">
                                            Fail
                                        </span>

                                    @else

                                        <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-sm">
                                            Pending
                                        </span>

                                    @endif

                                </td>

                                <!-- Remarks -->
                                <td class="px-4 py-4">

                                    <textarea
                                        name="marks[{{ $index }}][remarks]"
                                        rows="2"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2"
                                        placeholder="Remarks"
                                    >{{ optional($mark)->remarks }}</textarea>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7" class="px-4 py-10 text-center text-gray-500">

                                    No students found.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            <!-- Footer -->
            <div class="p-6 border-t bg-gray-50 flex items-center justify-between">

                <div class="text-sm text-gray-600">

                    Total Students:
                    <span class="font-semibold">
                        {{ count($students) }}
                    </span>

                </div>

                <button
                    type="submit"
                    class="bg-red-700 hover:bg-red-800 text-white px-6 py-3 rounded-lg font-semibold shadow"
                >
                    Save Marks
                </button>

            </div>

        </div>

    </form>

</div>

@endsection
