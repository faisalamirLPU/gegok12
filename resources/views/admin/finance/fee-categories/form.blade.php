<div>
    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Category Name <span class="text-red-500">*</span></label>
    <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}"
           placeholder="e.g. Tuition Fee, Sports Fee" required
           class="w-full border border-gray-300 rounded px-4 py-2 text-sm focus:border-blue-500 focus:ring-0">
</div>

<div>
    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Category Code</label>
    <input type="text" name="code" value="{{ old('code', $category->code ?? '') }}"
           placeholder="e.g. TUITION_FEE"
           class="w-full border border-gray-300 rounded px-4 py-2 text-sm font-mono focus:border-blue-500 focus:ring-0 uppercase">
</div>

<div>
    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Frequency <span class="text-red-500">*</span></label>
    <select name="frequency" required class="w-full border border-gray-300 rounded px-4 py-2 text-sm focus:border-blue-500 focus:ring-0">
        <option value="">Select Billing Cycle</option>
        <option value="monthly" {{ (old('frequency', $category->frequency ?? '') == 'monthly') ? 'selected' : '' }}>Monthly Collection</option>
        <option value="quarterly" {{ (old('frequency', $category->frequency ?? '') == 'quarterly') ? 'selected' : '' }}>Quarterly Collection</option>
        <option value="yearly" {{ (old('frequency', $category->frequency ?? '') == 'yearly') ? 'selected' : '' }}>Yearly Collection</option>
        <option value="one_time" {{ (old('frequency', $category->frequency ?? '') == 'one_time') ? 'selected' : '' }}>One Time Payment</option>
    </select>
</div>

<div>
    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Status</label>
    <select name="status" class="w-full border border-gray-300 rounded px-4 py-2 text-sm focus:border-blue-500 focus:ring-0">
        <option value="1" {{ (old('status', $category->status ?? 1) == 1) ? 'selected' : '' }}>Active - Visible in billing</option>
        <option value="0" {{ (old('status', $category->status ?? 1) == 0) ? 'selected' : '' }}>Inactive - Hidden from billing</option>
    </select>
</div>

<div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="bg-gray-50 border rounded p-4 hover:border-blue-200 transition">
        <label class="flex items-start gap-3 cursor-pointer">
            <input type="checkbox" name="is_refundable" value="1" {{ (old('is_refundable', $category->is_refundable ?? 0)) ? 'checked' : '' }}
                   class="mt-1 w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-0">
            <div>
                <div class="text-sm font-semibold text-gray-800">Refundable Category</div>
                <div class="text-[10px] text-gray-500 mt-0.5 leading-tight italic">If checked, this fee can be refunded to students under special circumstances.</div>
            </div>
        </label>
    </div>

    <div class="bg-gray-50 border rounded p-4 hover:border-blue-200 transition">
        <label class="flex items-start gap-3 cursor-pointer">
            <input type="checkbox" name="is_optional" value="1" {{ (old('is_optional', $category->is_optional ?? 0)) ? 'checked' : '' }}
                   class="mt-1 w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-0">
            <div>
                <div class="text-sm font-semibold text-gray-800">Optional Category</div>
                <div class="text-[10px] text-gray-500 mt-0.5 leading-tight italic">If checked, this fee is not mandatory and can be opted-out by students.</div>
            </div>
        </label>
    </div>
</div>

<div class="md:col-span-2">
    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Detailed Description</label>
    <textarea name="description" rows="3" placeholder="Provide details about what this fee covers..."
              class="w-full border border-gray-300 rounded px-4 py-2 text-sm focus:border-blue-500 focus:ring-0">{{ old('description', $category->description ?? '') }}</textarea>
</div>
