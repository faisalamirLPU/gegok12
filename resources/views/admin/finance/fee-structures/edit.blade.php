@extends('layouts.admin.layout')

@section('title', 'Edit Fee Structure')

@section('content')

<div class="relative">

    {{-- Header --}}
    <div class="flex flex-wrap lg:flex-row justify-between my-3">

        <div>
            <h1 class="admin-h1 my-3">
                Edit Fee Structure
            </h1>
        </div>

        <div class="flex items-center">

            <a href="{{ route('finance.fee-structures.index') }}"
               class="no-underline text-gray-600 px-4 flex items-center bg-gray-100 border rounded py-1.5 justify-center hover:bg-gray-200 transition">

                <span class="text-[10px] font-bold uppercase tracking-wider">
                    Back to Structures
                </span>

            </a>

        </div>

    </div>

    {{-- Navigation --}}
    @include('admin.finance.partials.tabs')

    {{-- Form --}}
    <form method="POST"
          action="{{ route('finance.fee-structures.update', $structure->id) }}"
          class="bg-white custom-shadow border overflow-hidden">

        @csrf
        @method('PUT')

        <input type="hidden"
               name="school_id"
               value="{{ auth()->user()->school_id }}">

        <input type="hidden"
               name="academic_year_id"
               value="{{ \App\Helpers\SiteHelper::getAcademicYear(auth()->user()->school_id)->id }}">

        {{-- Structure Information --}}
        <div class="p-6 border-b border-gray-100">

            <h2 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-6 border-b pb-1">
                Structure Information
            </h2>

            @include(
                'admin.finance.fee-structures.form',
                ['structure' => $structure]
            )

        </div>

        {{-- Fee Items --}}
        <div class="p-6 bg-gray-50/50">

            <div class="flex items-center justify-between mb-4">

                <div>

                    <h2 class="text-[10px] font-bold text-gray-800 uppercase tracking-wider">
                        Fee Structure Items
                    </h2>

                    <p class="text-[10px] text-gray-400 italic">
                        Configure category-wise fee amounts and due dates
                    </p>

                </div>

                <button type="button"
                        id="addFeeItem"
                        class="no-underline text-white px-4 flex items-center bg-blue-600 py-1.5 justify-center hover:bg-blue-700 transition">

                    <span class="text-[10px] font-bold uppercase tracking-wider">
                        + Add Fee Item
                    </span>

                </button>

            </div>

            <div class="bg-white border rounded-none overflow-hidden">

                <table class="w-full text-sm">

                    <thead>

                        <tr class="bg-gray-50 border-b">

                            <th class="px-4 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-gray-600">
                                Fee Category
                            </th>

                            <th class="px-4 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-gray-600 w-48">
                                Amount
                            </th>

                            <th class="px-4 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-gray-600 w-48">
                                Due Date
                            </th>

                            <th class="px-4 py-3 text-center text-[10px] font-bold uppercase tracking-wider text-gray-600 w-24">
                                Optional
                            </th>

                            <th class="px-4 py-3 text-center text-[10px] font-bold uppercase tracking-wider text-gray-600 w-20">
                                Action
                            </th>

                        </tr>

                    </thead>

                    <tbody id="feeItemsTable"
                           class="divide-y divide-gray-100">

                        @forelse($structure->items as $index => $item)

                            <tr class="fee-item-row hover:bg-gray-50 transition">

                                <td class="px-4 py-3">

                                    <select name="items[{{ $index }}][fee_category_id]"
                                            required
                                            class="w-full border border-gray-300 rounded-none px-4 py-2 text-xs focus:border-blue-500 focus:ring-0">

                                        <option value="">
                                            -- Select Category --
                                        </option>

                                        @foreach ($feeCategories as $category)

                                            <option value="{{ $category->id }}"
                                                {{ $item->fee_category_id == $category->id ? 'selected' : '' }}>

                                                {{ $category->name }}

                                            </option>

                                        @endforeach

                                    </select>

                                </td>

                                <td class="px-4 py-3">

                                    <input type="number"
                                           step="0.01"
                                           name="items[{{ $index }}][amount]"
                                           value="{{ $item->amount }}"
                                           required
                                           placeholder="0.00"
                                           class="amount-input w-full border border-gray-300 rounded-none px-4 py-2 text-xs focus:border-blue-500 focus:ring-0">

                                </td>

                                <td class="px-4 py-3">

                                    <input type="date"
                                           name="items[{{ $index }}][due_date]"
                                           value="{{ $item->due_date }}"
                                           class="w-full border border-gray-300 rounded-none px-4 py-2 text-xs focus:border-blue-500 focus:ring-0">

                                </td>

                                <td class="px-4 py-3 text-center">

                                    <input type="checkbox"
                                           name="items[{{ $index }}][is_optional]"
                                           value="1"
                                           {{ $item->is_optional ? 'checked' : '' }}
                                           class="w-4 h-4 rounded-none border-gray-300 text-blue-600 focus:ring-0">

                                </td>

                                <td class="px-4 py-3 text-center">

                                    <button type="button"
                                            class="remove-item text-red-500 hover:text-red-700 transition">

                                        <svg class="w-4 h-4"
                                             fill="none"
                                             stroke="currentColor"
                                             viewBox="0 0 24 24">

                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />

                                        </svg>

                                    </button>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5"
                                    class="text-center text-gray-400 py-8 text-sm">

                                    No fee items available

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- Total --}}
            <div class="mt-6 flex justify-end">

                <div class="bg-gray-100 border p-4 flex flex-col items-end">

                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest leading-none mb-1">
                        Total Structure Amount
                    </span>

                    <span id="totalAmount"
                          class="text-2xl font-bold text-gray-900 leading-none">

                        ₹0.00

                    </span>

                </div>

            </div>

        </div>

        {{-- Footer --}}
        <div class="bg-gray-50 border-t border-gray-100 p-6 flex items-center gap-4">

            <button type="submit"
                    class="no-underline text-white px-8 flex items-center custom-green py-2 justify-center">

                <span class="text-[10px] font-bold uppercase tracking-wider">
                    Update Fee Structure
                </span>

            </button>

            <a href="{{ route('finance.fee-structures.index') }}"
               class="no-underline text-gray-600 px-8 flex items-center bg-gray-100 border rounded py-2 justify-center hover:bg-gray-200 transition">

                <span class="text-[10px] font-bold uppercase tracking-wider">
                    Cancel
                </span>

            </a>

        </div>

    </form>

</div>

<script>

document.addEventListener('DOMContentLoaded', function() {

    const feeItemsTable = document.getElementById('feeItemsTable');

    const addFeeItemButton = document.getElementById('addFeeItem');

    const totalAmountElement = document.getElementById('totalAmount');

    let feeIndex = {{ $structure->items->count() }};

    const feeCategories = @json($feeCategories);

    /*
    |--------------------------------------------------------------------------
    | Calculate Total
    |--------------------------------------------------------------------------
    */

    const calculateTotal = function() {

        let total = 0;

        document.querySelectorAll('.amount-input').forEach(input => {

            total += parseFloat(input.value || 0);

        });

        totalAmountElement.innerText =
            `₹${total.toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            })}`;
    };

    /*
    |--------------------------------------------------------------------------
    | Build Category Options
    |--------------------------------------------------------------------------
    */

    const buildCategoryOptions = function() {

        let options =
            `<option value="">-- Select Category --</option>`;

        feeCategories.forEach(category => {

            options += `
                <option value="${category.id}">
                    ${category.name}
                </option>
            `;
        });

        return options;
    };

    /*
    |--------------------------------------------------------------------------
    | Add Row
    |--------------------------------------------------------------------------
    */

    const addFeeItemRow = function() {

        const row = document.createElement('tr');

        row.className =
            'fee-item-row hover:bg-gray-50 transition border-t border-gray-100';

        row.innerHTML = `

            <td class="px-4 py-3">

                <select name="items[${feeIndex}][fee_category_id]"
                        required
                        class="w-full border border-gray-300 rounded-none px-4 py-2 text-xs focus:border-blue-500 focus:ring-0">

                    ${buildCategoryOptions()}

                </select>

            </td>

            <td class="px-4 py-3">

                <input type="number"
                       step="0.01"
                       name="items[${feeIndex}][amount]"
                       required
                       placeholder="0.00"
                       class="amount-input w-full border border-gray-300 rounded-none px-4 py-2 text-xs focus:border-blue-500 focus:ring-0">

            </td>

            <td class="px-4 py-3">

                <input type="date"
                       name="items[${feeIndex}][due_date]"
                       class="w-full border border-gray-300 rounded-none px-4 py-2 text-xs focus:border-blue-500 focus:ring-0">

            </td>

            <td class="px-4 py-3 text-center">

                <input type="checkbox"
                       name="items[${feeIndex}][is_optional]"
                       value="1"
                       class="w-4 h-4 rounded-none border-gray-300 text-blue-600 focus:ring-0">

            </td>

            <td class="px-4 py-3 text-center">

                <button type="button"
                        class="remove-item text-red-500 hover:text-red-700 transition">

                    <svg class="w-4 h-4"
                         fill="none"
                         stroke="currentColor"
                         viewBox="0 0 24 24">

                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />

                    </svg>

                </button>

            </td>
        `;

        feeItemsTable.appendChild(row);

        feeIndex++;

        calculateTotal();
    };

    /*
    |--------------------------------------------------------------------------
    | Add Item
    |--------------------------------------------------------------------------
    */

    addFeeItemButton.addEventListener(
        'click',
        addFeeItemRow
    );

    /*
    |--------------------------------------------------------------------------
    | Amount Change
    |--------------------------------------------------------------------------
    */

    document.addEventListener('input', function(e) {

        if (e.target.classList.contains('amount-input')) {

            calculateTotal();

        }

    });

    /*
    |--------------------------------------------------------------------------
    | Remove Row
    |--------------------------------------------------------------------------
    */

    document.addEventListener('click', function(e) {

        const removeBtn =
            e.target.closest('.remove-item');

        if (removeBtn) {

            removeBtn.closest('tr').remove();

            calculateTotal();

        }

    });

    /*
    |--------------------------------------------------------------------------
    | Initial Total
    |--------------------------------------------------------------------------
    */

    calculateTotal();

});

</script>

@endsection