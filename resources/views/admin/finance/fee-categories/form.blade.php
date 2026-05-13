<div>
    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Category Name</label>
    <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}"
           placeholder="Enter category name"
           class="w-full border border-gray-300 rounded-none px-4 py-2 text-xs focus:border-blue-500 focus:ring-0">
</div>

<div>
    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Category Code</label>
    <input type="text" name="code" value="{{ old('code', $category->code ?? '') }}"
           placeholder="Example: TUITION_FEE"
           class="w-full border border-gray-300 rounded-none px-4 py-2 text-xs font-mono focus:border-blue-500 focus:ring-0">
</div>

<div>
    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Frequency</label>
    <select name="frequency" class="w-full border border-gray-300 rounded-none px-4 py-2 text-xs focus:border-blue-500 focus:ring-0">
        <option value="">Select Frequency</option>
        <option value="monthly" {{ (old('frequency', $category->frequency ?? '') == 'monthly') ? 'selected' : '' }}>Monthly</option>
        <option value="quarterly" {{ (old('frequency', $category->frequency ?? '') == 'quarterly') ? 'selected' : '' }}>Quarterly</option>
        <option value="yearly" {{ (old('frequency', $category->frequency ?? '') == 'yearly') ? 'selected' : '' }}>Yearly</option>
        <option value="one_time" {{ (old('frequency', $category->frequency ?? '') == 'one_time') ? 'selected' : '' }}>One Time</option>
    </select>
</div>

<div>
    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Status</label>
    <select name="status" class="w-full border border-gray-300 rounded-none px-4 py-2 text-xs focus:border-blue-500 focus:ring-0">
        <option value="1" {{ (old('status', $category->status ?? 1) == 1) ? 'selected' : '' }}>Active</option>
        <option value="0" {{ (old('status', $category->status ?? 1) == 0) ? 'selected' : '' }}>Inactive</option>
    </select>
</div>

<div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="border p-4">
        <label class="flex items-start gap-3 cursor-pointer">
            <input type="checkbox" name="is_refundable" value="1" {{ (old('is_refundable', $category->is_refundable ?? 0)) ? 'checked' : '' }}
                   class="mt-1 w-4 h-4 rounded-none border-gray-300 text-blue-600 focus:ring-0">
            <div>
                <div class="text-xs font-bold text-gray-800 uppercase tracking-wider">Refundable Fee</div>
                <div class="text-[10px] text-gray-400 mt-0.5 leading-tight italic">Students can request refunds for this category.</div>
            </div>
        </label>
    </div>

    <div class="border p-4">
        <label class="flex items-start gap-3 cursor-pointer">
            <input type="checkbox" name="is_optional" value="1" {{ (old('is_optional', $category->is_optional ?? 0)) ? 'checked' : '' }}
                   class="mt-1 w-4 h-4 rounded-none border-gray-300 text-blue-600 focus:ring-0">
            <div>
                <div class="text-xs font-bold text-gray-800 uppercase tracking-wider">Optional Fee</div>
                <div class="text-[10px] text-gray-400 mt-0.5 leading-tight italic">Students may choose whether to pay this fee.</div>
            </div>
        </label>
    </div>
</div>

<div class="md:col-span-2">
    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Description</label>
    <textarea name="description" rows="3" placeholder="Enter category description..."
              class="w-full border border-gray-300 rounded-none px-4 py-2 text-xs focus:border-blue-500 focus:ring-0">{{ old('description', $category->description ?? '') }}</textarea>
</div>
