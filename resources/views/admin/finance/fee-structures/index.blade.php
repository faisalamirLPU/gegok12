@extends('layouts.admin.layout')

@section('title', 'Fee Structures')

@section('content')

<div class="relative">
    {{-- Header --}}
    <div class="flex flex-wrap lg:flex-row justify-between my-3">
        <div>
            <h1 class="admin-h1 my-3">Fee Structures</h1>
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
               class="no-underline text-white px-4 flex items-center custom-green py-1.5 justify-center">
                <span class="text-[10px] font-bold uppercase tracking-wider">Add Structure</span>
            </a>
        </div>
    </div>

    @include('admin.finance.partials.tabs')


    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-2 mb-6">
        <button class="px-5 py-2 border text-[10px] font-bold uppercase tracking-widest bg-white text-gray-500 hover:bg-gray-50 transition installment-filter border-gray-200" data-filter="">All Definitions</button>
        <button class="px-5 py-2 border text-[10px] font-bold uppercase tracking-widest bg-white text-gray-500 hover:bg-gray-50 transition installment-filter border-gray-200" data-filter="monthly">Monthly</button>
        <button class="px-5 py-2 border text-[10px] font-bold uppercase tracking-widest bg-white text-gray-500 hover:bg-gray-50 transition installment-filter border-gray-200" data-filter="quarterly">Quarterly</button>
        <button class="px-5 py-2 border text-[10px] font-bold uppercase tracking-widest bg-white text-gray-500 hover:bg-gray-50 transition installment-filter border-gray-200" data-filter="yearly">Yearly</button>
    </div>

    {{-- Alerts --}}
    @include('admin.finance.partials.alerts')

    {{-- Table --}}
    <div class="bg-white custom-shadow border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-wider text-gray-600">Structure Title</th>
                        <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-wider text-gray-600">Academic Class</th>
                        <th class="px-5 py-4 text-left text-[10px] font-bold uppercase tracking-wider text-gray-600">Installment Type</th>
                        <th class="px-5 py-4 text-center text-[10px] font-bold uppercase tracking-wider text-gray-600">Deployment Status</th>
                        <th class="px-5 py-4 text-center text-[10px] font-bold uppercase tracking-wider text-gray-600">Administrative Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100" id="structureTable">
                    @forelse($structures as $structure)
                        <tr class="hover:bg-gray-50 transition structure-row">
                            <td class="px-5 py-3 font-bold text-gray-900 text-xs structure-title">
                                {{ $structure->title }}
                            </td>
                            <td class="px-5 py-3 text-[10px] font-bold text-gray-600 uppercase tracking-tight">
                                {{ $structure->class->name ?? 'Unassigned' }}
                            </td>
                            <td class="px-5 py-3 text-[10px] font-bold text-blue-600 uppercase tracking-widest installment-type">
                                {{ $structure->installment_type }}
                            </td>
                            <td class="px-5 py-3 text-center">
                                @if($structure->status)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-none text-[9px] font-bold bg-green-100 text-green-700 uppercase tracking-wider border border-green-200">Active</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-none text-[9px] font-bold bg-gray-100 text-gray-400 uppercase tracking-wider border border-gray-200">Inactive</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('finance.fee-structures.edit', $structure->id) }}" 
                                       class="p-1.5 text-blue-600 hover:bg-blue-50 transition border border-transparent hover:border-blue-100" title="Modify Structure">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <a href="#" class="p-1.5 text-gray-400 hover:bg-gray-50 transition border border-transparent hover:border-gray-200" title="View Structural Details">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        @include('admin.finance.partials.table-empty', ['message' => 'No fee structures defined for the selected academic cycle.'])
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

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
