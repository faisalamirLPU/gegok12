@extends('layouts.admin.layout')

@section('title', 'Fee Structures')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    {{-- Page Header --}}
    <div class="mb-5 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Fee Structures</h1>
            <p class="text-sm text-gray-500 mt-0.5">Configure fee structure per class and section</p>
        </div>
        <a href="{{ route('finance.fee-structures.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            New Structure
        </a>
    </div>

    {{-- Navigation --}}
    @include('admin.finance.partials.navigation')

    {{-- Structures Grid --}}
    <div class="grid grid-cols-1 gap-4 mt-5 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($structures as $structure)
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:border-blue-300 transition">
                {{-- Card Header --}}
                <div class="px-4 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold text-base">{{ optional($structure->standardLink->standard)->name ?? 'Unassigned' }}</h3>
                            <p class="text-blue-100 text-xs">{{ optional($structure->standardLink->section)->name ?? 'All Sections' }}</p>
                        </div>
                        <span class="px-2 py-0.5 bg-blue-500 bg-opacity-40 rounded text-xs font-semibold">
                            {{ ucfirst($structure->installment_type ?? 'Monthly') }}
                        </span>
                    </div>
                </div>

                {{-- Fee Items --}}
                <div class="p-4">
                    @php $total = $structure->items->sum('amount'); @endphp
                    <div class="space-y-1.5 mb-3">
                        @forelse($structure->items->take(4) as $item)
                            <div class="flex justify-between items-center text-sm py-1 border-b border-gray-50 last:border-0">
                                <span class="text-gray-700 truncate pr-2">{{ $item->feeCategory->name ?? 'Unknown' }}</span>
                                <span class="font-semibold text-gray-900 shrink-0">Rs. {{ number_format($item->amount, 0) }}</span>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 py-2">No fee items configured</p>
                        @endforelse
                        @if($structure->items->count() > 4)
                            <p class="text-xs text-gray-400 py-1">+{{ $structure->items->count() - 4 }} more items</p>
                        @endif
                    </div>

                    {{-- Total --}}
                    <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                        <span class="text-sm font-semibold text-gray-700">Total</span>
                        <span class="text-lg font-bold text-blue-700">Rs. {{ number_format($total, 0) }}</span>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="px-4 py-2.5 bg-gray-50 border-t border-gray-100 flex gap-2">
                    <a href="{{ route('finance.fee-structures.edit', $structure->id) }}" class="flex-1 text-center text-xs font-semibold text-blue-600 hover:bg-blue-100 py-1.5 rounded transition">
                        Edit
                    </a>
                    <form action="{{ route('finance.fee-structures.destroy', $structure->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Delete this structure?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full text-xs font-semibold text-red-600 hover:bg-red-100 py-1.5 rounded transition">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="bg-white rounded-lg border border-gray-200 p-12 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m8-4h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v-4a2 2 0 01-2-2H9a2 2 0 01-2 2v4h2m8-4v4m-10 8h2a2 2 0 012-2v-6a2 2 0 00-2-2h-4"/></svg>
                    <p class="font-medium text-gray-700">No fee structures found</p>
                    <p class="text-sm text-gray-400 mt-1 mb-4">Create structures to assign fees to classes</p>
                    <a href="{{ route('finance.fee-structures.create') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-lg text-sm transition">
                        Create First Structure
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($structures->hasPages())
        <div class="mt-5">
            {{ $structures->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection