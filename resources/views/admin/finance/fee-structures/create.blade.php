@extends('layouts.admin.layout')

@section('title', 'Create Fee Structure')

@section('content')

    <div class="max-w-7xl mx-auto px-4 py-6">

        <!-- Header -->
        <div class="mb-8">

            <h1 class="text-3xl font-bold text-gray-800">
                Create Fee Structure
            </h1>

            <p class="text-gray-600 mt-2">
                Configure class-wise finance structure for ERP billing engine
            </p>

        </div>

        @include('admin.finance.partials.alerts')

        <!-- Form -->
        <form method="POST" action="{{ route('finance.fee-structures.store') }}"
            class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">

            @csrf

            <input type="hidden" name="school_id" value="{{ auth()->user()->school_id }}">

            <input type="hidden" name="academic_year_id"
                value="{{ \App\Helpers\SiteHelper::getAcademicYear(auth()->user()->school_id)->id }}">

            <!-- Basic Details -->
            <div class="border-b border-gray-200 p-6 bg-gray-50">

                <h2 class="text-xl font-semibold text-gray-800 mb-6">
                    Structure Information
                </h2>

                @include('admin.finance.fee-structures.form', [
                    'structure' => null,
                ])

            </div>

            <!-- Fee Items -->
            <div class="p-6">

                <div class="flex items-center justify-between mb-6">

                    <div>

                        <h2 class="text-xl font-semibold text-gray-800">
                            Fee Structure Items
                        </h2>

                        <p class="text-sm text-gray-600 mt-1">
                            Configure category-wise fee amounts
                        </p>

                    </div>

                    <button type="button" id="addFeeItem"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                        + Add Fee Item
                    </button>

                </div>

                <!-- Table -->
                <div class="overflow-x-auto border border-gray-200 rounded-xl">

                    <table class="min-w-full">

                        <thead class="bg-gray-100 border-b border-gray-200">

                            <tr>

                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">
                                    Fee Category
                                </th>

                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">
                                    Amount
                                </th>

                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">
                                    Due Date
                                </th>

                                <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">
                                    Optional
                                </th>

                                <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">
                                    Action
                                </th>

                            </tr>

                        </thead>

                        <tbody id="feeItemsTable">

                            <tr class="fee-item-row border-b border-gray-200">

                                <!-- Category -->
                                <td class="px-4 py-4">

                                    <select name="items[0][fee_category_id]" required
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-600 focus:outline-none">

                                        <option value="">
                                            -- Select Category --
                                        </option>

                                        @foreach ($feeCategories as $category)
                                            <option value="{{ $category->id }}">

                                                {{ $category->name }}

                                            </option>
                                        @endforeach

                                    </select>

                                </td>

                                <!-- Amount -->
                                <td class="px-4 py-4">

                                    <input type="number" step="0.01" name="items[0][amount]" required placeholder="0.00"
                                        class="amount-input w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-600 focus:outline-none">

                                </td>

                                <!-- Due Date -->
                                <td class="px-4 py-4">

                                    <input type="date" name="items[0][due_date]"
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-600 focus:outline-none">

                                </td>

                                <!-- Optional -->
                                <td class="px-4 py-4 text-center">

                                    <input type="checkbox" name="items[0][is_optional]" value="1"
                                        class="w-5 h-5 rounded border-gray-300 text-red-600 focus:ring-red-500">

                                </td>

                                <!-- Remove -->
                                <td class="px-4 py-4 text-center">

                                    <button type="button"
                                        class="remove-item bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-sm"
                                        style="display:none;">
                                        Remove
                                    </button>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

                <!-- Total -->
                <div class="mt-6 flex justify-end">

                    <div class="bg-gray-100 rounded-xl px-6 py-4 shadow-sm">

                        <div class="text-sm text-gray-600">
                            Total Structure Amount
                        </div>

                        <div id="totalAmount" class="text-3xl font-bold text-gray-800 mt-1">
                            ₹0.00
                        </div>

                    </div>

                </div>

            </div>

            <!-- Footer -->
            <div class="bg-gray-50 border-t border-gray-200 p-6 flex items-center gap-4">

                <button type="submit"
                    class="bg-red-700 hover:bg-red-800 text-white px-6 py-3 rounded-lg font-semibold shadow transition">
                    Save Fee Structure
                </button>

                <a href="{{ route('finance.fee-structures.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold transition">
                    Cancel
                </a>

            </div>

        </form>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const feeItemsTable = document.getElementById('feeItemsTable');
            const addFeeItemButton = document.getElementById('addFeeItem');
            const totalAmountElement = document.getElementById('totalAmount');

            let feeIndex = 1;

            const feeCategories = @json($feeCategories);

            const calculateTotal = function() {

                let total = 0;

                document.querySelectorAll('.amount-input').forEach(input => {

                    total += parseFloat(input.value || 0);

                });

                totalAmountElement.innerText = `₹${total.toFixed(2)}`;
            };

            const buildCategoryOptions = function() {

                let options = `<option value="">-- Select Category --</option>`;

                feeCategories.forEach(category => {

                    options += `
                <option value="${category.id}">
                    ${category.name}
                </option>
            `;
                });

                return options;
            };

            const addFeeItemRow = function() {

                const row = document.createElement('tr');

                row.className = 'fee-item-row border-b border-gray-200';

                row.innerHTML = `

            <td class="px-4 py-4">

                <select
                    name="items[${feeIndex}][fee_category_id]"
                    required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-600 focus:outline-none"
                >

                    ${buildCategoryOptions()}

                </select>

            </td>

            <td class="px-4 py-4">

                <input
                    type="number"
                    step="0.01"
                    name="items[${feeIndex}][amount]"
                    required
                    placeholder="0.00"
                    class="amount-input w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-600 focus:outline-none"
                >

            </td>

            <td class="px-4 py-4">

                <input
                    type="date"
                    name="items[${feeIndex}][due_date]"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-600 focus:outline-none"
                >

            </td>

            <td class="px-4 py-4 text-center">

                <input
                    type="checkbox"
                    name="items[${feeIndex}][is_optional]"
                    value="1"
                    class="w-5 h-5 rounded border-gray-300 text-red-600 focus:ring-red-500"
                >

            </td>

            <td class="px-4 py-4 text-center">

                <button
                    type="button"
                    class="remove-item bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-sm"
                >
                    Remove
                </button>

            </td>
        `;

                feeItemsTable.appendChild(row);

                feeIndex++;

                calculateTotal();
            };

            addFeeItemButton.addEventListener('click', function() {

                addFeeItemRow();

            });

            document.addEventListener('input', function(e) {

                if (e.target.classList.contains('amount-input')) {

                    calculateTotal();

                }

            });

            document.addEventListener('click', function(e) {

                if (e.target.classList.contains('remove-item')) {

                    e.target.closest('tr').remove();

                    calculateTotal();
                }
            });

            calculateTotal();

        });
    </script>

@endsection
