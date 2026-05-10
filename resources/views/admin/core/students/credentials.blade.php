@extends('layouts.admin.layout')

@section('content')

<div>

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-4">

        <div>
            <h1 class="admin-h1 font-plex my-3">
                Student Login Credentials
            </h1>

            <p class="text-gray-500">
                School admins can reset or generate student passwords.
            </p>
        </div>

        {{-- SEARCH BAR --}}
        <div class="mt-3 lg:mt-0">

            <input
                type="text"
                id="studentSearch"
                placeholder="Search by student name or email..."
                class="border border-gray-300 rounded px-4 py-2 w-full lg:w-80 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >

        </div>

    </div>

    @include('partials.message')

    <div class="bg-white custom-shadow border overflow-x-auto">

        <table class="w-full" id="studentTable">

            <thead>

                <tr class="border-b bg-gray-50">

                    <th class="text-left py-3 px-4">
                        Student
                    </th>

                    <th class="text-left py-3 px-4">
                        Login Email
                    </th>

                    <th class="text-left py-3 px-4">
                        New Password
                    </th>

                    <th class="text-left py-3 px-4">
                        Action
                    </th>

                </tr>

            </thead>

            <tbody id="studentTableBody">

                @foreach($students as $student)

                    <tr class="border-b hover:bg-gray-50 student-row">

                        {{-- STUDENT NAME --}}
                        <td class="py-3 px-4 student-name">

                            {{ optional($student->userprofile)->firstname }}
                            {{ optional($student->userprofile)->lastname }}

                        </td>

                        {{-- EMAIL --}}
                        <td class="py-3 px-4 student-email">

                            {{ $student->email }}

                        </td>

                        {{-- RESET PASSWORD --}}
                        <td class="py-3 px-4">

                            <form
                                method="POST"
                                action="{{ url('/admin/student-credentials/'.$student->id.'/reset') }}"
                                class="flex gap-2"
                            >

                                @csrf

                                <input
                                    name="password"
                                    placeholder="Leave blank to auto-generate"
                                    class="border px-3 py-2 w-64 rounded"
                                >

                                <button
                                    class="bg-red-700 hover:bg-red-800 text-white px-3 py-2 rounded transition"
                                >

                                    Reset

                                </button>

                            </form>

                        </td>

                        {{-- PROFILE --}}
                        <td class="py-3 px-4">

                            <a
                                class="text-blue-600 hover:text-blue-800 font-medium"
                                href="{{ url('/admin/student/show/'.$student->name) }}"
                            >

                                Profile

                            </a>

                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

        {{-- NO RESULTS --}}
        <div
            id="noResults"
            class="hidden text-center py-6 text-gray-500"
        >

            No students found.

        </div>

    </div>

    {{-- PAGINATION --}}
    <div class="mt-4">

        {{ $students->links() }}

    </div>

</div>

{{-- SEARCH SCRIPT --}}
<script>

document.addEventListener('DOMContentLoaded', function () {

    const searchInput =
        document.getElementById('studentSearch');

    const rows =
        document.querySelectorAll('.student-row');

    const noResults =
        document.getElementById('noResults');

    searchInput.addEventListener('keyup', function () {

        let searchValue =
            this.value.toLowerCase().trim();

        let visibleRows = 0;

        rows.forEach(function (row) {

            let studentName =
                row.querySelector('.student-name')
                    .innerText
                    .toLowerCase();

            let studentEmail =
                row.querySelector('.student-email')
                    .innerText
                    .toLowerCase();

            if (
                studentName.includes(searchValue)
                ||
                studentEmail.includes(searchValue)
            ) {

                row.style.display = '';

                visibleRows++;

            } else {

                row.style.display = 'none';

            }

        });

        /*
        |--------------------------------------------------------------------------
        | No Results
        |--------------------------------------------------------------------------
        */

        if (visibleRows === 0) {

            noResults.classList.remove('hidden');

        } else {

            noResults.classList.add('hidden');

        }

    });

});

</script>

@endsection
