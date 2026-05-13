@extends('layouts.admin.layout')

@section('content')

<div class="relative">
    {{-- Header --}}
    <div class="flex flex-wrap lg:flex-row justify-between my-3">
        <div>
            <h1 class="admin-h1 my-3">Fee Categories</h1>
        </div>
        <div class="flex items-center gap-2">
            <div class="relative">
                <input type="text" id="searchInput" placeholder="Search categories..."
                       class="rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-0 w-64 pl-8">
                <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <a href="{{ route('finance.fee-categories.create') }}"
               class="no-underline text-white px-4 flex items-center custom-green py-1.5 justify-center">
                <span class="text-[10px] font-bold uppercase tracking-wider">Add Category</span>
            </a>
        </div>
    </div>

    @include('admin.finance.partials.tabs')

    {{-- Table --}}
    <div class="mt-6 bg-white custom-shadow border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-wider text-gray-600">Category Identifier</th>
                        <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-wider text-gray-600">Alpha Code</th>
                        <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-wider text-gray-600">Billing Cycle</th>
                        <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-wider text-gray-600">Refund Policy</th>
                        <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-wider text-gray-600">Compliance</th>
                        <th class="px-5 py-4 text-center text-[10px] font-bold uppercase tracking-wider text-gray-600">Status</th>
                        <th class="px-5 py-4 text-center text-[10px] font-bold uppercase tracking-wider text-gray-600">Administrative Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="feeCategoryTable">
                    @forelse($feeCategories as $category)
                        <tr class="hover:bg-gray-50 transition fee-row">
                            <td class="px-5 py-3 font-bold text-gray-900 text-xs category-name">
                                {{ $category->name }}
                            </td>
                            <td class="px-5 py-3">
                                <span class="px-1.5 py-0.5 bg-gray-100 border border-gray-200 text-[10px] font-bold text-gray-500 font-mono">
                                    {{ $category->code }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-[10px] text-gray-600 uppercase font-bold tracking-tight">
                                {{ str_replace('_', ' ', $category->frequency) }}
                            </td>
                            <td class="px-5 py-3">
                                @if($category->is_refundable)
                                    <span class="text-[9px] font-bold text-green-600 uppercase tracking-widest bg-green-50 px-2 py-0.5 border border-green-100">Refundable</span>
                                @else
                                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest bg-gray-50 px-2 py-0.5 border border-gray-200 opacity-60">Non-Refundable</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                @if($category->is_optional)
                                    <span class="text-[9px] font-bold text-blue-500 uppercase tracking-widest bg-blue-50 px-2 py-0.5 border border-blue-100">Optional</span>
                                @else
                                    <span class="text-[9px] font-bold text-red-500 uppercase tracking-widest bg-red-50 px-2 py-0.5 border border-red-100">Mandatory</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center">
                                @if($category->status)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-none text-[9px] font-bold bg-green-100 text-green-700 uppercase tracking-wider border border-green-200">Active</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-none text-[9px] font-bold bg-gray-100 text-gray-500 uppercase tracking-wider border border-gray-200">Inactive</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('finance.fee-categories.edit', $category->id) }}" 
                                       class="p-1.5 text-blue-600 hover:bg-blue-50 transition border border-transparent hover:border-blue-100" title="Edit Configuration">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('finance.fee-categories.destroy', $category->id) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 transition border border-transparent hover:border-red-100" 
                                                title="Remove Category" onclick="return confirm('Confirm permanent deletion of this fee category?')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        @include('admin.finance.partials.table-empty', ['message' => 'No financial fee categories configured for the current session.'])
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

        </table>

    </div>

</div>


{{-- Dynamic Search --}}
<script>

document.addEventListener('DOMContentLoaded', function () {

    const searchInput =
        document.getElementById('searchInput');

    const rows =
        document.querySelectorAll('.fee-row');

    searchInput.addEventListener('keyup', function () {

        const value =
            this.value.toLowerCase();

        rows.forEach(row => {

            const name =
                row.querySelector('.category-name')
                   .innerText
                   .toLowerCase();

            row.style.display =
                name.includes(value)
                    ? ''
                    : 'none';

        });

    });

});

</script>

@endsection
