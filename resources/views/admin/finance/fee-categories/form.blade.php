<div>

    <label class="block text-sm font-semibold text-gray-700 mb-2">
        Category Name
    </label>

    <input type="text"
           name="name"
           value="{{ old('name', $category->name ?? '') }}"
           placeholder="Enter category name"
           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-600 focus:outline-none">

</div>


<div>

    <label class="block text-sm font-semibold text-gray-700 mb-2">
        Category Code
    </label>

    <input type="text"
           name="code"
           value="{{ old('code', $category->code ?? '') }}"
           placeholder="Example: TUITION_FEE"
           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-600 focus:outline-none">

</div>


<div>

    <label class="block text-sm font-semibold text-gray-700 mb-2">
        Frequency
    </label>

    <select name="frequency"
            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-600 focus:outline-none">

        <option value="">
            Select Frequency
        </option>

        <option value="monthly">
            Monthly
        </option>

        <option value="quarterly">
            Quarterly
        </option>

        <option value="yearly">
            Yearly
        </option>

        <option value="one_time">
            One Time
        </option>

    </select>

</div>


<div>

    <label class="block text-sm font-semibold text-gray-700 mb-2">
        Status
    </label>

    <select name="status"
            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-600 focus:outline-none">

        <option value="1">
    Active
</option>

<option value="0">
    Inactive
</option>

    </select>

</div>


<div class="md:col-span-2">

    <div class="border border-gray-300 rounded-lg p-4">

        <label class="flex items-start gap-3">

            <input type="checkbox"
                   name="is_refundable"
                   value="1"
                   class="mt-1 w-5 h-5 rounded border-gray-300 text-red-600 focus:ring-red-500">

            <div>

                <div class="font-medium text-gray-700">
                    Refundable Fee
                </div>

                <div class="text-sm text-gray-500 mt-1">
                    Students can request refunds for this category.
                </div>

            </div>

        </label>

    </div>

</div>


<div class="md:col-span-2">

    <div class="border border-gray-300 rounded-lg p-4">

        <label class="flex items-start gap-3">

            <input type="checkbox"
                   name="is_optional"
                   value="1"
                   class="mt-1 w-5 h-5 rounded border-gray-300 text-red-600 focus:ring-red-500">

            <div>

                <div class="font-medium text-gray-700">
                    Optional Fee
                </div>

                <div class="text-sm text-gray-500 mt-1">
                    Students may choose whether to pay this fee.
                </div>

            </div>

        </label>

    </div>

</div>


<div class="md:col-span-2">

    <label class="block text-sm font-semibold text-gray-700 mb-2">
        Description
    </label>

    <textarea name="description"
              rows="5"
              placeholder="Enter category description..."
              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-600 focus:outline-none">{{ old('description', $category->description ?? '') }}</textarea>

</div>
