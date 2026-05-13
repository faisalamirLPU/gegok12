<!-- Payment Recording Modal -->
<div id="payment-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-none custom-shadow border w-full max-w-lg mx-4">
        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Record Payment</h3>
                <button type="button" onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <form id="payment-form" action="{{ route('finance.fee-records.payment') }}" method="POST">
            @csrf
            <input type="hidden" name="fee_id" id="payment-fee-id" value="">
            <div class="p-6 space-y-4">
                <div class="bg-gray-50 border p-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase">Student</p>
                            <p id="payment-student-name" class="text-xs font-bold text-gray-800">-</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase">Balance Due</p>
                            <p id="payment-balance" class="text-xs font-bold text-red-600">Rs. 0.00</p>
                        </div>
                    </div>
                </div>

                {{-- Payment Amount --}}
                <div>
                    <label for="payment-amount" class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">
                        Payment Amount <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400">Rs.</span>
                        <input type="number"
                               step="0.01"
                               min="1"
                               name="amount"
                               id="payment-amount"
                               required
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-none text-xs focus:border-blue-500 focus:ring-0"
                               placeholder="Enter amount">
                    </div>
                    <p class="mt-1 text-[10px] text-gray-400 italic">
                        Amount exceeding balance will be recorded as advance credit
                    </p>
                </div>

                {{-- Excess Warning --}}
                <div id="payment-excess-warning" class="hidden bg-blue-50 border border-blue-100 text-blue-700 px-4 py-3">
                    <p class="text-[10px] leading-tight">
                        <strong>Note:</strong> The amount entered exceeds the balance. The excess will be automatically recorded as advance credit for future payments.
                    </p>
                </div>

                {{-- Payment Method --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="payment-method" class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">
                            Method <span class="text-red-500">*</span>
                        </label>
                        <select name="payment_method"
                                id="payment-method"
                                required
                                class="w-full border border-gray-300 rounded-none py-2 px-3 text-xs focus:border-blue-500 focus:ring-0">
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="upi">UPI</option>
                            <option value="card">Card</option>
                            <option value="cheque">Cheque</option>
                            <option value="dd">Demand Draft</option>
                        </select>
                    </div>

                    <div>
                        <label for="payment-date" class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">
                            Date
                        </label>
                        <input type="date"
                               name="payment_date"
                               id="payment-date"
                               value="{{ now()->format('Y-m-d') }}"
                               class="w-full border border-gray-300 rounded-none py-2 px-3 text-xs focus:border-blue-500 focus:ring-0">
                    </div>
                </div>

                {{-- Transaction ID --}}
                <div>
                    <label for="transaction-id" class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">
                        Transaction ID / Reference
                    </label>
                    <input type="text"
                           name="transaction_id"
                           id="transaction-id"
                           class="w-full border border-gray-300 rounded-none py-2 px-3 text-xs focus:border-blue-500 focus:ring-0"
                           placeholder="Optional">
                </div>

                {{-- Remarks --}}
                <div>
                    <label for="remarks" class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">
                        Remarks
                    </label>
                    <textarea name="remarks"
                              id="remarks"
                              rows="2"
                              class="w-full border border-gray-300 rounded-none py-2 px-3 text-xs focus:border-blue-500 focus:ring-0"
                              placeholder="Optional notes..."></textarea>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t flex items-center justify-end gap-3">
                <button type="button"
                        onclick="closePaymentModal()"
                        class="px-6 py-1.5 border border-gray-300 text-xs font-bold uppercase tracking-wider text-gray-600 hover:bg-gray-100 transition">
                    Cancel
                </button>
                <button type="submit"
                        class="no-underline text-white px-8 flex items-center custom-green py-1.5 justify-center">
                    <span class="text-xs font-bold uppercase tracking-wider">Record Payment</span>
                </button>
            </div>
        </form>
    </div>
</div>
        </form>
    </div>
</div>