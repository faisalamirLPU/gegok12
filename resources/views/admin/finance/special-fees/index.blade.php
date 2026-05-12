@extends('layouts.admin.layout')

@section('title', 'Special Fees')

@section('content')


<div class="max-w-7xl mx-auto px-4 py-6">

    <div class="flex items-center justify-between mb-6">

        <div>

            <h1 class="text-2xl font-bold text-gray-800">
                Special Fee Assignments
            </h1>

            <p class="text-gray-500 mt-1">
                Hostel, transport and optional fee management
            </p>

        </div>

        <a href="{{ route('finance.special-fees.create') }}"
           class="bg-red-700 hover:bg-red-800 text-white px-5 py-3 rounded-lg font-semibold">

            Assign Special Fee

        </a>

    </div>
<div class="flex justify-end mb-5">

    <form method="GET">

        <input type="text"
               name="search"
               value="{{ request('search') }}"
               placeholder="Search student..."
               class="border border-gray-300 rounded-lg px-4 py-2 w-72">

    </form>

</div>
    <div class="bg-white shadow rounded-xl overflow-hidden border border-gray-200">


        <table class="w-full">

            <thead class="bg-gray-100">

                <tr>

                    <th class="px-5 py-3 text-left">
                        Student
                    </th>

                    <th class="px-5 py-3 text-left">
                        Fee Category
                    </th>

                    <th class="px-5 py-3 text-left">
                        Amount
                    </th>

                    <th class="px-5 py-3 text-left">
                        Due Date
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($fees as $fee)

                    <tr class="border-t">

                        <td class="px-5 py-4">

                            {{
                                optional(
                                    $fee->student->userprofile
                                )->firstname
                            }}

                            {{
                                optional(
                                    $fee->student->userprofile
                                )->lastname
                            }}

                        </td>

                        <td class="px-5 py-4">
                            {{ $fee->feeCategory->name ?? '-' }}
                        </td>

                        <td class="px-5 py-4 font-semibold text-red-700">
                            ₹{{ number_format($fee->amount, 2) }}
                        </td>

                        <td class="px-5 py-4">
                            {{ optional($fee->due_date)->format('d M Y') }}
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="4"
                            class="text-center py-10 text-gray-500">

                            No special fee assignments found.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <div class="mt-5">

        {{ $fees->links() }}

    </div>

</div>

@endsection
