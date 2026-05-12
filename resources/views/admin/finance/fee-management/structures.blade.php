@extends('layouts.admin.layout')

@section('title', 'Fee Structures Management')

@section('content')

<div class="max-w-6xl mx-auto px-4 py-6">

    <!-- Header -->
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Fee Structures</h1>
            <p class="text-gray-600">Configure fee structure for each class/section</p>
        </div>
        <a href="{{ route('finance.fee-structures.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded-lg transition">
            📊 New Structure
        </a>
    </div>

    <!-- Navigation Tabs -->
    <div class="mb-6 border-b border-gray-200">
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('finance.fee-management.index') }}" class="px-4 py-3 border-b-2 border-transparent text-gray-600 hover:text-blue-600 hover:border-blue-600">Dashboard</a>
            <a href="{{ route('finance.fee-management.categories') }}" class="px-4 py-3 border-b-2 border-transparent text-gray-600 hover:text-blue-600 hover:border-blue-600">Categories</a>
            <a href="{{ route('finance.fee-management.structures') }}" class="px-4 py-3 border-b-2 border-blue-600 text-blue-600 font-semibold">Structures</a>
            <a href="{{ route('finance.fee-management.special-fees') }}" class="px-4 py-3 border-b-2 border-transparent text-gray-600 hover:text-blue-600 hover:border-blue-600">Special Fees</a>
            <a href="{{ route('finance.fee-management.payments') }}" class="px-4 py-3 border-b-2 border-transparent text-gray-600 hover:text-blue-600 hover:border-blue-600">Payments</a>
            <a href="{{ route('finance.fee-management.analytics') }}" class="px-4 py-3 border-b-2 border-transparent text-gray-600 hover:text-blue-600 hover:border-blue-600">Analytics</a>
        </div>
    </div>

    <!-- Structures Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($structures as $structure)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4 text-white">
                    <h3 class="text-lg font-bold">{{ optional($structure->standardLink->standard)->name ?? 'N/A' }}</h3>
                    <p class="text-blue-100 text-sm">{{ optional($structure->standardLink->section)->name ?? 'All Sections' }}</p>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <p class="text-gray-600 text-sm mb-2">Fee Categories:</p>
                        <div class="space-y-2">
                            @forelse ($structure->items as $item)
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-700">{{ optional($item->feeCategory)->name }}</span>
                                    <span class="font-semibold text-gray-800">₹{{ number_format($item->amount, 2) }}</span>
                                </div>
                            @empty
                                <p class="text-gray-500 text-sm">No items</p>
                            @endforelse
                        </div>
                    </div>
                    <div class="border-t pt-4 text-sm">
                        <div class="flex justify-between items-center mb-4">
                            <span class="font-semibold text-gray-700">Total:</span>
                            <span class="text-lg font-bold text-blue-600">₹{{ number_format($structure->items->sum('amount'), 2) }}</span>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('finance.fee-structures.edit', $structure->id) }}" class="flex-1 text-center bg-blue-100 text-blue-600 hover:bg-blue-200 font-semibold py-2 rounded transition">
                                Edit
                            </a>
                            <form action="{{ route('finance.fee-structures.destroy', $structure->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-red-100 text-red-600 hover:bg-red-200 font-semibold py-2 rounded transition">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="bg-white rounded-lg shadow-md p-12 text-center">
                    <p class="text-gray-500 text-lg mb-4">No fee structures found</p>
                    <a href="{{ route('finance.fee-structures.create') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                        Create your first structure
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $structures->links() }}
    </div>

</div>

@endsection
