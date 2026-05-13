@extends('layouts.admin.layout')

@section('title', 'Fee Structures')

@section('content')
<div class="relative">
    {{-- Page Header --}}
    <div class="flex flex-wrap lg:flex-row justify-between my-3">
        <div>
            <h1 class="admin-h1 my-3">Fee Structures</h1>
            <p class="text-xs text-gray-500 font-medium italic">Define and manage fee definitions for academic classes.</p>
        </div>
        <div class="flex items-center gap-2">
            <div class="relative">
                <input type="text" id="searchInput" placeholder="Search structures..."
                       class="rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-0 w-64 pl-8">
                <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <a href="{{ route('finance.fee-structures.create') }}"
               class="no-underline text-white px-4 flex items-center custom-green py-2 justify-center shadow-sm hover:shadow-md transition">
                <span class="text-xs font-bold uppercase tracking-wider">Add Structure</span>
            </a>
        </div>
    </div>

    {{-- Navigation Tabs --}}
    @include('admin.finance.partials.tabs')

    {{-- Quick Filters --}}
    <div class="flex flex-wrap items-center gap-2 my-6">
        <button class="px-5 py-2 border text-[10px] font-bold uppercase tracking-widest bg-white text-gray-500 hover:bg-gray-50 transition installment-filter border-gray-200" data-filter="">All Definitions</button>
        <button class="px-5 py-2 border text-[10px] font-bold uppercase tracking-widest bg-white text-gray-500 hover:bg-gray-50 transition installment-filter border-gray-200" data-filter="monthly">Monthly</button>
        <button class="px-5 py-2 border text-[10px] font-bold uppercase tracking-widest bg-white text-gray-500 hover:bg-gray-50 transition installment-filter border-gray-200" data-filter="quarterly">Quarterly</button>
        <button class="px-5 py-2 border text-[10px] font-bold uppercase tracking-widest bg-white text-gray-500 hover:bg-gray-50 transition installment-filter border-gray-200" data-filter="yearly">Yearly</button>
    </div>

    {{-- Main Table --}}
    <div class="bg-white custom-shadow border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-600">Structure Title</th>
                        <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-600">Academic Class</th>
                        <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-600">Installment Type</th>
                        <th class="px-5 py-4 text-center text-xs font-bold uppercase tracking-wider text-gray-600">Deployment Status</th>
                        <th class="px-5 py-4 text-center text-xs font-bold uppercase tracking-wider text-gray-600">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100" id="structureTable">
                    @forelse($structures as $structure)
                        <tr class="hover:bg-gray-50 transition structure-row">
                            <td class="px-5 py-4">
                                <div class="font-bold text-gray-900 text-xs structure-title">{{ $structure->title }}</div>
                                <div class="text-[10px] text-gray-400 mt-0.5 uppercase font-medium">UID: #{{ str_pad($structure->id, 4, '0', STR_PAD_LEFT) }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-xs font-semibold text-gray-700 bg-gray-50 px-2 py-1 border border-gray-200 rounded">
                                    {{ $structure->class->name ?? 'Global' }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-[10px] font-bold text-blue-600 uppercase tracking-widest installment-type">
                                    {{ $structure->installment_type }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($structure->status)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-green-100 text-green-700 uppercase tracking-wider border border-green-200">Active</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-gray-100 text-gray-400 uppercase tracking-wider border border-gray-200">Inactive</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('finance.fee-structures.edit', $structure->id) }}" 
                                       class="p-1.5 text-blue-600 hover:bg-blue-50 transition border border-transparent hover:border-blue-100 rounded" title="Modify Structure">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('finance.fee-structures.destroy', $structure->id) }}" method="POST" class="inline" onsubmit="return confirm('Archive this fee structure?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 transition border border-transparent hover:border-red-100 rounded" title="Archive Structure">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        @include('admin.finance.partials.table-empty', ['message' => 'No financial fee structures found.'])
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Dynamic Search + Filter --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const rows = document.querySelectorAll('.structure-row');
    const filterButtons = document.querySelectorAll('.installment-filter');
    let activeFilter = '';

    function filterTable() {
        const search = searchInput.value.toLowerCase();
        rows.forEach(row => {
            const title = row.querySelector('.structure-title').innerText.toLowerCase();
            const installment = row.querySelector('.installment-type').innerText.toLowerCase();
            const matchesSearch = title.includes(search);
            const matchesFilter = activeFilter === '' ? true : installment.includes(activeFilter);
            row.style.display = matchesSearch && matchesFilter ? '' : 'none';
        });
    }

    searchInput.addEventListener('keyup', filterTable);
    filterButtons.forEach(button => {
        button.addEventListener('click', function () {
            activeFilter = this.dataset.filter;
            filterTable();
        });
    });
});
</script>
@endsection

        </table>

    </div>

</div>


{{-- Dynamic Search + Filter --}}
<script>

document.addEventListener('DOMContentLoaded', function () {

    const searchInput =
        document.getElementById('searchInput');

    const rows =
        document.querySelectorAll('.structure-row');

    const filterButtons =
        document.querySelectorAll('.installment-filter');

    let activeFilter = '';

    function filterTable() {

        const search =
            searchInput.value.toLowerCase();

        rows.forEach(row => {

            const title =
                row.querySelector('.structure-title')
                   .innerText
                   .toLowerCase();

            const installment =
                row.querySelector('.installment-type')
                   .innerText
                   .toLowerCase();

            const matchesSearch =
                title.includes(search);

            const matchesFilter =
                activeFilter === ''
                    ? true
                    : installment.includes(activeFilter);

            row.style.display =
                matchesSearch && matchesFilter
                    ? ''
                    : 'none';

        });

    }

    searchInput.addEventListener('keyup', filterTable);

    filterButtons.forEach(button => {

        button.addEventListener('click', function () {

            activeFilter =
                this.dataset.filter;

            filterTable();

        });

    });

});

</script>

@endsection
