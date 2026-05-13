<!-- Payment Recording Modal -->
<div id="payment-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Record Payment</h3>
                <button type="button" onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <form id="payment-form" action="{{ route('finance.fee-records.payment') }}" method="POST">
            @csrf
            <input type="hidden" name="fee_id" id="payment-fee-id" value="">
            <div class="p-6 space-y-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500">Student</p>
                            <p id="payment-student-name" class="font-semibold text-gray-900">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Balance Due</p>
                            <p id="payment-balance" class="font-semibold text-red-600">Rs. 0.00</p>
                        </div>
                    </div>
                </div>

                {{-- Payment Amount --}}
                <div>
                    <label for="payment-amount" class="block text-sm font-medium text-gray-700 mb-1">
                        Payment Amount <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rs.</span>
                        <input type="number"
                               step="0.01"
                               min="1"
                               name="amount"
                               id="payment-amount"
                               required
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Enter amount">
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        Amount exceeding balance will be recorded as advance credit
                    </p>
                </div>

                {{-- Excess Warning --}}
                <div id="payment-excess-warning" class="hidden bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg">
                    <p class="text-sm">
                        <strong>Note:</strong> The amount entered exceeds the balance. The excess will be automatically recorded as advance credit for future payments.
                    </p>
                </div>

                {{-- Payment Method --}}
                <div>
                    <label for="payment-method" class="block text-sm font-medium text-gray-700 mb-1">
                        Payment Method <span class="text-red-500">*</span>
                    </label>
                    <select name="payment_method"
                            id="payment-method"
                            required
                            class="w-full border border-gray-300 rounded-md py-2 px-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="upi">UPI</option>
                        <option value="card">Card</option>
                        <option value="cheque">Cheque</option>
                        <option value="dd">Demand Draft</option>
                    </select>
                </div>

                {{-- Transaction ID --}}
                <div>
                    <label for="transaction-id" class="block text-sm font-medium text-gray-700 mb-1">
                        Transaction ID / Reference
                    </label>
                    <input type="text"
                           name="transaction_id"
                           id="transaction-id"
                           class="w-full border border-gray-300 rounded-md py-2 px-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Optional">
                </div>

                {{-- Payment Date --}}
                <div>
                    <label for="payment-date" class="block text-sm font-medium text-gray-700 mb-1">
                        Payment Date
                    </label>
                    <input type="date"
                           name="payment_date"
                           id="payment-date"
                           value="{{ now()->format('Y-m-d') }}"
                           class="w-full border border-gray-300 rounded-md py-2 px-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                {{-- Remarks --}}
                <div>
                    <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">
                        Remarks
                    </label>
                    <textarea name="remarks"
                              id="remarks"
                              rows="2"
                              class="w-full border border-gray-300 rounded-md py-2 px-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Optional notes..."></textarea>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-3">
                <button type="button"
                        onclick="closePaymentModal()"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 font-medium">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 font-medium">
                    Record Payment
                </button>
            </div>
        </form>
    </div>
</div>