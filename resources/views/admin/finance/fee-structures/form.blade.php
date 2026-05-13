<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- Structure Title --}}
    <div>
        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Structure Title <span class="text-red-500">*</span></label>
        <input type="text" name="title" value="{{ old('title', $structure->title ?? '') }}"
               placeholder="e.g. Class 10 - Academic Year 2026" required
               class="w-full border border-gray-300 rounded px-4 py-2 text-sm focus:border-blue-500 focus:ring-0">
        @error('title')
            <p class="text-red-500 text-[10px] mt-1 font-medium italic">{{ $message }}</p>
        @enderror
    </div>

    {{-- Class Assignment --}}
    <div>
        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Target Academic Class <span class="text-red-500">*</span></label>
        @php $uniqueClasses = $classes->unique('standard_id'); @endphp
        <select name="class_id" required class="w-full border border-gray-300 rounded px-4 py-2 text-sm focus:border-blue-500 focus:ring-0">
            <option value="">Select Target Class</option>
            @foreach ($uniqueClasses as $class)
                <option value="{{ $class->standard_id }}" {{ old('class_id', $structure->class_id ?? '') == $class->standard_id ? 'selected' : '' }}>
                    {{ $class->standard->name ?? 'Standard Class' }}
                </option>
            @endforeach
        </select>
        @error('class_id')
            <p class="text-red-500 text-[10px] mt-1 font-medium italic">{{ $message }}</p>
        @enderror
    </div>

    {{-- Section Filter --}}
    <div>
        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Section Coverage</label>
        <select name="section_id" class="w-full border border-gray-300 rounded px-4 py-2 text-sm focus:border-blue-500 focus:ring-0">
            <option value="">Apply to All Sections</option>
            @foreach ($sections as $section)
                <option value="{{ $section->id }}" {{ old('section_id', $structure->section_id ?? '') == $section->id ? 'selected' : '' }}>
                    {{ $section->name }} Section
                </option>
            @endforeach
        </select>
    </div>

    {{-- Installment Type --}}
    <div>
        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Installment Frequency <span class="text-red-500">*</span></label>
        <select name="installment_type" required class="w-full border border-gray-300 rounded px-4 py-2 text-sm focus:border-blue-500 focus:ring-0">
            <option value="monthly" {{ old('installment_type', $structure->installment_type ?? '') == 'monthly' ? 'selected' : '' }}>Monthly Billing</option>
            <option value="quarterly" {{ old('installment_type', $structure->installment_type ?? '') == 'quarterly' ? 'selected' : '' }}>Quarterly Billing</option>
            <option value="half_yearly" {{ old('installment_type', $structure->installment_type ?? '') == 'half_yearly' ? 'selected' : '' }}>Half-Yearly Billing</option>
            <option value="yearly" {{ old('installment_type', $structure->installment_type ?? '') == 'yearly' ? 'selected' : '' }}>Yearly Billing</option>
            <option value="custom" {{ old('installment_type', $structure->installment_type ?? '') == 'custom' ? 'selected' : '' }}>Custom Arrangement</option>
        </select>
    </div>

    {{-- Due Date Strategy --}}
    <div>
        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Due Date Calculation <span class="text-red-500">*</span></label>
        <select name="due_type" required class="w-full border border-gray-300 rounded px-4 py-2 text-sm focus:border-blue-500 focus:ring-0">
            <option value="monthly_cycle" {{ old('due_type', $structure->due_type ?? '') == 'monthly_cycle' ? 'selected' : '' }}>Relative to Month Cycle</option>
            <option value="fixed_date" {{ old('due_type', $structure->due_type ?? '') == 'fixed_date' ? 'selected' : '' }}>Specific Calendar Date</option>
            <option value="custom" {{ old('due_type', $structure->due_type ?? '') == 'custom' ? 'selected' : '' }}>Manual Override</option>
        </select>
    </div>

    {{-- Monthly Due Day --}}
    <div>
        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Standard Due Day</label>
        <input type="number" min="1" max="31" name="due_day" value="{{ old('due_day', $structure->due_day ?? '') }}"
               placeholder="e.g. 10" class="w-full border border-gray-300 rounded px-4 py-2 text-sm focus:border-blue-500 focus:ring-0">
        <p class="text-[10px] text-gray-400 mt-1 italic leading-tight">Determines the automatic due date for monthly billing cycles (1-28).</p>
    </div>

    {{-- Lifecycle Status --}}
    <div>
        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Deployment Status</label>
        <select name="status" class="w-full border border-gray-300 rounded px-4 py-2 text-sm focus:border-blue-500 focus:ring-0">
            <option value="1" {{ old('status', $structure->status ?? 1) == 1 ? 'selected' : '' }}>Active - In Use</option>
            <option value="0" {{ old('status', $structure->status ?? 1) == 0 ? 'selected' : '' }}>Inactive - Archived</option>
        </select>
    </div>

    {{-- Structural Description --}}
    <div class="md:col-span-2">
        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Structural Details & Remarks</label>
        <textarea name="description" rows="3" placeholder="Provide additional context for this fee structure..."
                  class="w-full border border-gray-300 rounded px-4 py-2 text-sm focus:border-blue-500 focus:ring-0">{{ old('description', $structure->description ?? '') }}</textarea>
    </div>
</div>

</div>
