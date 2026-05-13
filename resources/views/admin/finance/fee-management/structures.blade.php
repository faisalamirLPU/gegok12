@extends('layouts.admin.layout')

@section('title', 'Fee Structures')

@section('content')
<div class="relative">
    {{-- Page Header --}}
    <div class="flex flex-wrap lg:flex-row justify-between my-3">
        <div>
            <h1 class="admin-h1 my-3">Fee Structures</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('finance.fee-structures.create') }}"
               class="no-underline text-white px-4 my-3 mx-1 flex items-center custom-green py-1 justify-center">
                <span class="mx-1 text-sm font-semibold">Add Structure</span>
                <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 409.6 409.6" xml:space="preserve" class="w-3 h-3 fill-current text-white"><g><g><path d="M392.533,187.733H221.867V17.067C221.867,7.641,214.226,0,204.8,0s-17.067,7.641-17.067,17.067v170.667H17.067
                  C7.641,187.733,0,195.374,0,204.8s7.641,17.067,17.067,17.067h170.667v170.667c0,9.426,7.641,17.067,17.067,17.067
                  s17.067-7.641,17.067-17.067V221.867h170.667c9.426,0,17.067-7.641,17.067-17.067S401.959,187.733,392.533,187.733z"></path></g></g></svg>
            </a>
        </div>
    </div>

    {{-- Navigation --}}
    @include('admin.finance.partials.tabs')

    {{-- Structures Grid --}}
    <div class="flex flex-wrap -mx-1 mt-4">
        @forelse ($structures as $structure)
            <div class="w-full lg:w-1/3 md:w-1/2 px-1 my-2">
                <div class="bg-white custom-shadow border h-full flex flex-col">
                    {{-- Card Header --}}
                    <div class="px-4 py-3 border-b bg-gray-50 flex items-center justify-between">
                        <div>
                            <h3 class="font-semibold text-gray-800">{{ optional($structure->standardLink->standard)->name ?? 'Unassigned' }}</h3>
                            <p class="text-gray-500 text-xs">{{ optional($structure->standardLink->section)->name ?? 'All Sections' }}</p>
                        </div>
                        <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-semibold">
                            {{ ucfirst($structure->installment_type ?? 'Monthly') }}
                        </span>
                    </div>

                    {{-- Fee Items --}}
                    <div class="p-4 flex-grow">
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
                        <div class="flex justify-between items-center pt-2 border-t">
                            <span class="text-sm font-semibold text-gray-700">Total</span>
                            <span class="text-lg font-bold text-blue-700">Rs. {{ number_format($total, 0) }}</span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="px-4 py-2.5 bg-gray-50 border-t flex gap-2">
                        <a href="{{ route('finance.fee-structures.edit', $structure->id) }}" class="flex-1 text-center text-xs font-semibold text-blue-600 hover:bg-blue-100 py-1.5 border border-blue-200 rounded transition bg-white">
                            Edit
                        </a>
                        <form action="{{ route('finance.fee-structures.destroy', $structure->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Delete this structure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full text-xs font-semibold text-red-600 hover:bg-red-100 py-1.5 border border-red-200 rounded transition bg-white">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="w-full px-1">
                <div class="bg-white custom-shadow border p-12 text-center my-2">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m8-4h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v-4a2 2 0 01-2-2H9a2 2 0 01-2 2v4h2m8-4v4m-10 8h2a2 2 0 012-2v-6a2 2 0 00-2-2h-4"/></svg>
                    <p class="font-medium text-gray-700">No fee structures found</p>
                    <p class="text-sm text-gray-400 mt-1 mb-4">Create structures to assign fees to classes</p>
                    <a href="{{ route('finance.fee-structures.create') }}" class="no-underline text-white px-4 py-2 inline-flex items-center custom-green justify-center">
                        <span class="text-sm font-semibold">Create First Structure</span>
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