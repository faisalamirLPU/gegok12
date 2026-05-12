@extends('layouts.admin.layout')

@section('title', 'Special Fees Management')

@section('content')

<div class="max-w-6xl mx-auto px-4 py-6">

    <!-- Header -->
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Special Fees</h1>
            <p class="text-gray-600">Assign additional fees to specific students</p>
        </div>
        <a href="{{ route('finance.special-fees.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-6 rounded-lg transition">
            ⚡ Assign Fee
        </a>
    </div>

    <!-- Navigation Tabs -->
    <div class="mb-6 border-b border-gray-200">
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('finance.fee-management.index') }}" class="px-4 py-3 border-b-2 border-transparent text-gray-600 hover:text-blue-600 hover:border-blue-600">Dashboard</a>
            <a href="{{ route('finance.fee-management.categories') }}" class="px-4 py-3 border-b-2 border-transparent text-gray-600 hover:text-blue-600 hover:border-blue-600">Categories</a>
            <a href="{{ route('finance.fee-management.structures') }}" class="px-4 py-3 border-b-2 border-transparent text-gray-600 hover:text-blue-600 hover:border-blue-600">Structures</a>
            <a href="{{ route('finance.fee-management.special-fees') }}" class="px-4 py-3 border-b-2 border-blue-600 text-blue-600 font-semibold">Special Fees</a>
            <a href="{{ route('finance.fee-management.payments') }}" class="px-4 py-3 border-b-2 border-transparent text-gray-600 hover:text-blue-600 hover:border-blue-600">Payments</a>
            <a href="{{ route('finance.fee-management.analytics') }}" class="px-4 py-3 border-b-2 border-transparent text-gray-600 hover:text-blue-600 hover:border-blue-600">Analytics</a>
        </div>
    </div>

    <!-- Special Fees Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Student</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Category</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">Amount</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Due Date</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($specialFees as $fee)
                        <tr class="border-t hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm">
                                <div class="font-semibold text-gray-800">
                                    {{ optional($fee->student->userprofile)->firstname }}
                                    {{ optional($fee->student->userprofile)->lastname }}
                                </div>
                                <div class="text-gray-500 text-xs">ID: {{ optional($fee->student)->roll_number ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ optional($fee->feeCategory)->name }}</td>
                            <td class="px-6 py-4 text-right text-sm font-semibold text-gray-800">₹{{ number_format($fee->amount, 2) }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if ($fee->due_date)
                                    <span class="text-gray-700">{{ $fee->due_date->format('M d, Y') }}</span>
                                    @if ($fee->due_date < now())
                                        <span class="ml-2 px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded">Overdue</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $fee->status ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $fee->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('finance.special-fees.edit', $fee->id) }}" class="text-blue-600 hover:text-blue-800 font-semibold text-sm">Edit</a>
                                <form action="{{ route('finance.special-fees.destroy', $fee->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ml-3 text-red-600 hover:text-red-800 font-semibold text-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <p class="text-lg mb-2">No special fees assigned</p>
                                <a href="{{ route('finance.special-fees.create') }}" class="text-blue-600 hover:text-blue-800">Assign a special fee</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $specialFees->links() }}
    </div>

</div>

@endsection
