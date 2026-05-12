@extends('layouts.admin.layout')

@section('title', 'Fee Categories Management')

@section('content')

<div class="max-w-6xl mx-auto px-4 py-6">

    <!-- Header -->
    <div class="mb-8 flex justify-between items-center">

        <div>
            <h1 class="text-3xl font-bold text-gray-800">
                Fee Categories
            </h1>

            <p class="text-gray-600">
                Manage fee categories for your school
            </p>
        </div>

        <a href="{{ route('finance.fee-categories.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition">

            ➕ New Category

        </a>

    </div>

    <!-- Navigation Tabs -->
    <div class="mb-6 border-b border-gray-200">

        <div class="flex flex-wrap gap-4">

            <a href="{{ route('finance.fee-management.index') }}"
               class="px-4 py-3 border-b-2 border-transparent text-gray-600 hover:text-blue-600 hover:border-blue-600">

                Dashboard

            </a>

            <a href="{{ route('finance.fee-management.categories') }}"
               class="px-4 py-3 border-b-2 border-blue-600 text-blue-600 font-semibold">

                Categories

            </a>

            <a href="{{ route('finance.fee-management.structures') }}"
               class="px-4 py-3 border-b-2 border-transparent text-gray-600 hover:text-blue-600 hover:border-blue-600">

                Structures

            </a>

            <a href="{{ route('finance.fee-management.special-fees') }}"
               class="px-4 py-3 border-b-2 border-transparent text-gray-600 hover:text-blue-600 hover:border-blue-600">

                Special Fees

            </a>

            <a href="{{ route('finance.fee-management.payments') }}"
               class="px-4 py-3 border-b-2 border-transparent text-gray-600 hover:text-blue-600 hover:border-blue-600">

                Payments

            </a>

            <a href="{{ route('finance.fee-management.analytics') }}"
               class="px-4 py-3 border-b-2 border-transparent text-gray-600 hover:text-blue-600 hover:border-blue-600">

                Analytics

            </a>

        </div>

    </div>

    <!-- Categories Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead class="bg-gray-100">

                    <tr>

                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">
                            Category Name
                        </th>

                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">
                            Description
                        </th>

                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">
                            Status
                        </th>

                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">
                            Created
                        </th>

                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">
                            Actions
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse ($feeCategories as $category)

                        <tr class="border-t hover:bg-gray-50 transition">

                            <td class="px-6 py-4 text-sm font-semibold text-gray-800">
                                {{ $category->name }}
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-600">

                                {{ \Illuminate\Support\Str::limit($category->description, 50) }}

                            </td>

                            <td class="px-6 py-4 text-sm">

                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $category->status
                                        ? 'bg-green-100 text-green-800'
                                        : 'bg-red-100 text-red-800'
                                    }}">

                                    {{ $category->status
                                        ? 'Active'
                                        : 'Inactive'
                                    }}

                                </span>

                            </td>

                            <td class="px-6 py-4 text-sm text-gray-600">

                                {{ optional($category->created_at)->format('M d, Y') }}

                            </td>

                            <td class="px-6 py-4 text-center">

                                <a href="{{ route('finance.fee-categories.edit', $category->id) }}"
                                   class="text-blue-600 hover:text-blue-800 font-semibold">

                                    Edit

                                </a>

                                <form action="{{ route('finance.fee-categories.destroy', $category->id) }}"
                                      method="POST"
                                      class="inline"
                                      onsubmit="return confirm('Are you sure?')">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="ml-4 text-red-600 hover:text-red-800 font-semibold">

                                        Delete

                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5"
                                class="px-6 py-8 text-center">

                                <div class="text-gray-500">

                                    <p class="text-lg mb-2">
                                        No fee categories found
                                    </p>

                                    <a href="{{ route('finance.fee-categories.create') }}"
                                       class="text-blue-600 hover:text-blue-800">

                                        Create your first category

                                    </a>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    <!-- Pagination -->
    <div class="mt-6">

        {{ $feeCategories->links() }}

    </div>

</div>

@endsection
