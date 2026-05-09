@extends('layouts.admin.layout')

@section('content')

<div class="p-6 bg-gray-100 min-h-screen">

    {{-- Page Header --}}
    <div class="mb-6">

        <h1 class="text-4xl font-bold text-gray-800">
            SaaS Super Admin Dashboard
        </h1>

        <p class="text-gray-600 mt-2">
            Welcome back, Super Admin
        </p>

    </div>


    {{-- Statistics --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">

        {{-- Total Schools --}}
        <div class="bg-white rounded-2xl shadow-md p-6">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-gray-500 text-sm">
                        Total Schools
                    </p>

                    <h2 class="text-4xl font-bold mt-2">
                        {{ $totalSchools ?? 0 }}
                    </h2>
                </div>

                <div class="text-5xl">
                    🏫
                </div>

            </div>

        </div>


        {{-- Active Schools --}}
        <div class="bg-white rounded-2xl shadow-md p-6">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-gray-500 text-sm">
                        Active Schools
                    </p>

                    <h2 class="text-4xl font-bold mt-2">
                        {{ $activeSchools ?? 0 }}
                    </h2>
                </div>

                <div class="text-5xl">
                    ✅
                </div>

            </div>

        </div>


        {{-- Total Students --}}
        <div class="bg-white rounded-2xl shadow-md p-6">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-gray-500 text-sm">
                        Students
                    </p>

                    <h2 class="text-4xl font-bold mt-2">
                        {{ $students ?? 0 }}
                    </h2>
                </div>

                <div class="text-5xl">
                    🎓
                </div>

            </div>

        </div>


        {{-- Teachers --}}
        <div class="bg-white rounded-2xl shadow-md p-6">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-gray-500 text-sm">
                        Teachers
                    </p>

                    <h2 class="text-4xl font-bold mt-2">
                        {{ $teachers ?? 0 }}
                    </h2>
                </div>

                <div class="text-5xl">
                    👨‍🏫
                </div>

            </div>

        </div>

    </div>



    {{-- Second Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

        {{-- Subscription Overview --}}
        <div class="bg-white rounded-2xl shadow-md p-6">

            <h2 class="text-xl font-semibold mb-4">
                Subscription Overview
            </h2>

            <div class="space-y-4">

                <div class="flex justify-between">
                    <span>Total Subscriptions</span>
                    <strong>{{ $subscriptions ?? 0 }}</strong>
                </div>

                <div class="flex justify-between">
                    <span>Expired Plans</span>
                    <strong>{{ $expiredPlans ?? 0 }}</strong>
                </div>

                <div class="flex justify-between">
                    <span>Total Plans</span>
                    <strong>{{ $plans ?? 0 }}</strong>
                </div>

            </div>

        </div>



        {{-- Recent Schools --}}
        <div class="bg-white rounded-2xl shadow-md p-6 lg:col-span-2">

            <div class="flex justify-between items-center mb-4">

                <h2 class="text-xl font-semibold">
                    Recent Schools
                </h2>

                <button class="bg-red-700 text-white px-4 py-2 rounded-lg">
                    View All
                </button>

            </div>


            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead>

                        <tr class="border-b">

                            <th class="text-left py-3">
                                School
                            </th>

                            <th class="text-left py-3">
                                Email
                            </th>

                            <th class="text-left py-3">
                                Status
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($recentSchools as $school)

                            <tr class="border-b hover:bg-gray-50">

                                <td class="py-4">
                                    {{ $school->name }}
                                </td>

                                <td class="py-4">
                                    {{ $school->email }}
                                </td>

                                <td class="py-4">

                                    @if($school->status)

                                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">
                                            Active
                                        </span>

                                    @else

                                        <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm">
                                            Inactive
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="3" class="py-6 text-center text-gray-500">
                                    No schools found
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection
