@extends('layouts.admin.layout')

@section('content')

<div>

    <!-- Header -->
    <div class="flex items-center justify-between mb-5">

        <div>
            <h1 class="admin-h1 font-plex my-3">
                {{ $exam->name }}
            </h1>

            <p class="text-gray-500">
                {{ optional($exam->standardLink)->StandardSection }}
                |
                {{ ucwords($exam->status) }}
            </p>
        </div>

        @if((int) auth()->user()->usergroup_id !== \App\Models\User::TEACHER_USERGROUP_ID)

            <form
                method="POST"
                action="{{ url('/admin/exams/'.$exam->id.'/publish') }}"
            >
                @csrf

                <button
                    class="bg-green-700 hover:bg-green-800 text-white px-5 py-2 rounded-lg transition"
                >
                    Publish
                </button>

            </form>

        @endif

    </div>

    @include('partials.message')

    <!-- Subject Statistics -->
    <div class="bg-white custom-shadow border overflow-x-auto mb-6 rounded-xl">

        <table class="w-full">

            <thead>

                <tr class="border-b bg-gray-50">

                    <th class="text-left py-4 px-4">
                        Subject
                    </th>

                    <th class="text-left py-4 px-4">
                        Max
                    </th>

                    <th class="text-left py-4 px-4">
                        Pass
                    </th>

                    <th class="text-left py-4 px-4">
                        Marks Entered
                    </th>

                    <th class="text-left py-4 px-4">
                        Pass
                    </th>

                    <th class="text-left py-4 px-4">
                        Fail
                    </th>

                    <th class="text-left py-4 px-4">
                        Action
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($exam->subjects as $subject)

                    <tr class="border-b hover:bg-gray-50">

                        <td class="py-4 px-4 font-medium">
                            {{ $subject->subject_name }}
                        </td>

                        <td class="py-4 px-4">
                            {{ number_format($subject->max_marks, 2) }}
                        </td>

                        <td class="py-4 px-4">
                            {{ number_format($subject->pass_marks, 2) }}
                        </td>

                        <td class="py-4 px-4">

                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm">

                                {{ $subject->marks_entered }}

                            </span>

                        </td>

                        <td class="py-4 px-4">

                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">

                                {{ $subject->pass_count }}

                            </span>

                        </td>

                        <td class="py-4 px-4">

                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm">

                                {{ $subject->fail_count }}

                            </span>

                        </td>

                        <td class="py-4 px-4">

                            <a
                                class="text-blue-600 hover:text-blue-800 font-medium"
                                href="{{ url((request()->segment(1) === 'teacher' ? '/teacher' : '/admin').'/exams/'.$exam->id.'/subjects/'.$subject->id.'/marks') }}"
                            >
                                Upload Marks
                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="7"
                            class="text-center py-8 text-gray-500"
                        >
                            No subjects found
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <!-- Students Section -->
    <!-- Students Section -->
<div class="bg-white custom-shadow border overflow-hidden rounded-xl">

    <!-- Header -->
    <div class="p-4 border-b bg-gray-50">

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

            <div>

                <h2 class="text-xl font-semibold">

                    Students ({{ $students->total() }})

                </h2>

                <p class="text-gray-500 text-sm mt-1">

                    Student-wise result completion status

                </p>

            </div>

            <!-- Search -->
            <form
    method="GET"
    id="studentSearchForm"
    class="flex items-center gap-2"
>

    <input
        type="text"
        name="search"
        id="studentSearchInput"
        value="{{ request('search') }}"
        placeholder="Search student by name or roll..."
        class="border rounded-lg px-4 py-2 w-64 focus:ring focus:ring-blue-100"
    >

    <button
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg"
    >
        Search
    </button>

</form>

        </div>

        <!-- Statistics -->
        <div class="mt-4 flex flex-wrap gap-3">

            <span class="bg-green-100 text-green-700 px-4 py-2 rounded-full text-sm font-medium">

                Completed:
                {{ $students->where('result_completed', true)->count() }}

            </span>

            <span class="bg-yellow-100 text-yellow-700 px-4 py-2 rounded-full text-sm font-medium">

                Pending:
                {{ $students->where('result_completed', false)->count() }}

            </span>

        </div>

    </div>

    <!-- Table -->
    <div class="overflow-x-auto">

        <table class="w-full">

            <thead>

                <tr class="border-b bg-gray-50">

                    <th class="text-left py-4 px-4">
                        Roll No
                    </th>

                    <th class="text-left py-4 px-4">
                        Student Name
                    </th>

                    <th class="text-left py-4 px-4">
                        Marks Status
                    </th>

                    <th class="text-left py-4 px-4">
                        Action
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($students as $student)

                    <tr class="border-b hover:bg-gray-50 transition">

                        <!-- Roll -->
                        <td class="py-4 px-4">

                            {{ $student->registration_number ?? '-' }}

                        </td>

                        <!-- Name -->
                        <td class="py-4 px-4">

                            <div class="flex items-center">

                                <img
                                    src="{{ asset(optional($student->userprofile)->avatar ?? 'uploads/default.png') }}"
                                    class="w-10 h-10 rounded-full mr-3 border"
                                >

                                <div>

                                    <div class="font-medium">

                                        {{ optional($student->userprofile)->firstname }}
                                        {{ optional($student->userprofile)->lastname }}

                                    </div>

                                    <div class="text-sm text-gray-500">

                                        ID:
                                        {{ $student->id }}

                                    </div>

                                </div>

                            </div>

                        </td>

                        <!-- Status -->
                        <td class="py-4 px-4">

                            @if($student->result_completed)

                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-semibold">

                                    Completed

                                </span>

                            @else

                                <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm font-semibold">

                                    {{ $student->marks_count }}/{{ $student->total_subjects }}
                                    Subjects

                                </span>

                            @endif

                        </td>

                        <!-- Action -->
                        <td class="py-4 px-4">

                            @if($student->result_completed)

                                <a
                                    href="{{ route('admin.exams.marksheet', [
                                        'exam' => $exam->id,
                                        'student' => $student->id
                                    ]) }}"
                                    class="text-blue-600 hover:text-blue-800 font-medium"
                                    target="_blank"
                                >
                                    View Marksheet
                                </a>

                            @else

                                <span class="text-gray-400">

                                    Pending

                                </span>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="4"
                            class="py-10 px-4 text-center text-gray-500"
                        >

                            No students found

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <!-- Pagination -->
    <div class="p-4 border-t bg-gray-50">

        {{ $students->links() }}

    </div>

</div>

</div>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const input =
        document.getElementById(
            'studentSearchInput'
        );

    const form =
        document.getElementById(
            'studentSearchForm'
        );

    input.addEventListener('input', function () {

        /*
        |--------------------------------------------------------------------------
        | Auto Reset When Empty
        |--------------------------------------------------------------------------
        */

        if (this.value.trim() === '') {

            window.location =
                window.location.pathname;
        }
    });
});

</script>

@endsection
