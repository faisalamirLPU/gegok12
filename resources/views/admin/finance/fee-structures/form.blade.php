<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Structure Title -->
    <div>
        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Structure Title</label>
        <input type="text" name="title" value="{{ old('title', $structure->title ?? '') }}"
               placeholder="Example: Class 10 - 2026 Structure" required
               class="w-full border border-gray-300 rounded-none px-4 py-2 text-xs focus:border-blue-500 focus:ring-0">
        @error('title')
            <p class="text-red-600 text-[10px] mt-1 italic">{{ $message }}</p>
        @enderror
    </div>

    <!-- Class -->
    <div>
        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Class</label>
        @php $uniqueClasses = $classes->unique('standard_id'); @endphp
        <select name="class_id" required class="w-full border border-gray-300 rounded-none px-4 py-2 text-xs focus:border-blue-500 focus:ring-0">
            <option value="">Select Class</option>
            @foreach ($uniqueClasses as $class)
                <option value="{{ $class->standard_id }}" {{ old('class_id', $structure->class_id ?? '') == $class->standard_id ? 'selected' : '' }}>
                    {{ $class->standard->name ?? '-' }}
                </option>
            @endforeach
        </select>
        @error('class_id')
            <p class="text-red-600 text-[10px] mt-1 italic">{{ $message }}</p>
        @enderror
    </div>

    <!-- Section -->
    <div>
        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Section</label>
        <select name="section_id" class="w-full border border-gray-300 rounded-none px-4 py-2 text-xs focus:border-blue-500 focus:ring-0">
            <option value="">All Sections</option>
            @foreach ($sections as $section)
                <option value="{{ $section->id }}" {{ old('section_id', $structure->section_id ?? '') == $section->id ? 'selected' : '' }}>
                    {{ $section->name }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Installment Type -->
    <div>
        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Installment Type</label>
        <select name="installment_type" required class="w-full border border-gray-300 rounded-none px-4 py-2 text-xs focus:border-blue-500 focus:ring-0">
            <option value="monthly" {{ old('installment_type', $structure->installment_type ?? '') == 'monthly' ? 'selected' : '' }}>Monthly</option>
            <option value="quarterly" {{ old('installment_type', $structure->installment_type ?? '') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
            <option value="half_yearly" {{ old('installment_type', $structure->installment_type ?? '') == 'half_yearly' ? 'selected' : '' }}>Half Yearly</option>
            <option value="yearly" {{ old('installment_type', $structure->installment_type ?? '') == 'yearly' ? 'selected' : '' }}>Yearly</option>
            <option value="custom" {{ old('installment_type', $structure->installment_type ?? '') == 'custom' ? 'selected' : '' }}>Custom</option>
        </select>
    </div>

    <!-- Due Type -->
    <div>
        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Due Type</label>
        <select name="due_type" required class="w-full border border-gray-300 rounded-none px-4 py-2 text-xs focus:border-blue-500 focus:ring-0">
            <option value="monthly_cycle" {{ old('due_type', $structure->due_type ?? '') == 'monthly_cycle' ? 'selected' : '' }}>Monthly Cycle</option>
            <option value="fixed_date" {{ old('due_type', $structure->due_type ?? '') == 'fixed_date' ? 'selected' : '' }}>Fixed Date</option>
            <option value="custom" {{ old('due_type', $structure->due_type ?? '') == 'custom' ? 'selected' : '' }}>Custom</option>
        </select>
    </div>

    <!-- Due Day -->
    <div>
        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Due Day</label>
        <input type="number" min="1" max="31" name="due_day" value="{{ old('due_day', $structure->due_day ?? '') }}"
               placeholder="Example: 10" class="w-full border border-gray-300 rounded-none px-4 py-2 text-xs focus:border-blue-500 focus:ring-0">
        <p class="text-[10px] text-gray-400 mt-1 italic italic leading-none">Monthly due day for automatic invoice calculations</p>
    </div>

    <!-- Status -->
    <div>
        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Status</label>
        <select name="status" class="w-full border border-gray-300 rounded-none px-4 py-2 text-xs focus:border-blue-500 focus:ring-0">
            <option value="1" {{ old('status', $structure->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
            <option value="0" {{ old('status', $structure->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>

    <!-- Description -->
    <div class="md:col-span-2">
        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Description</label>
        <textarea name="description" rows="3" placeholder="Add fee structure description..."
                  class="w-full border border-gray-300 rounded-none px-4 py-2 text-xs focus:border-blue-500 focus:ring-0">{{ old('description', $structure->description ?? '') }}</textarea>
    </div>
</div>

</div>
