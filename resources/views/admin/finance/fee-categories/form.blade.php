<div class="row">

    <div class="col-md-6 mb-4">

        <label class="form-label fw-semibold">
            Category Name
        </label>

        <input type="text"
               name="name"
               class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $feeCategory->name ?? '') }}"
               placeholder="Enter category name"
               required>

        @error('name')

            <div class="invalid-feedback">

                {{ $message }}

            </div>

        @enderror

    </div>

    <div class="col-md-6 mb-4">

        <label class="form-label fw-semibold">
            Category Code
        </label>

        <input type="text"
               name="code"
               class="form-control @error('code') is-invalid @enderror"
               value="{{ old('code', $feeCategory->code ?? '') }}"
               placeholder="Example: TUTION_FEE"
               required>

        @error('code')

            <div class="invalid-feedback">

                {{ $message }}

            </div>

        @enderror

    </div>

    <div class="col-md-6 mb-4">

        <label class="form-label fw-semibold">
            Frequency
        </label>

        <select name="frequency"
                class="form-select @error('frequency') is-invalid @enderror">

            <option value="">
                Select Frequency
            </option>

            <option value="one_time">
                One Time
            </option>

            <option value="monthly">
                Monthly
            </option>

            <option value="quarterly">
                Quarterly
            </option>

            <option value="half_yearly">
                Half Yearly
            </option>

            <option value="yearly">
                Yearly
            </option>

        </select>

        @error('frequency')

            <div class="invalid-feedback">

                {{ $message }}

            </div>

        @enderror

    </div>

    <div class="col-md-6 mb-4">

        <label class="form-label fw-semibold">
            Status
        </label>

        <select name="status"
                class="form-select">

            <option value="1">
                Active
            </option>

            <option value="0">
                Inactive
            </option>

        </select>

    </div>

    <div class="col-md-6 mb-4">

        <div class="card border">

            <div class="card-body">

                <div class="form-check">

                    <input type="checkbox"
                           name="is_refundable"
                           value="1"
                           class="form-check-input"
                           id="is_refundable"

                           {{ old('is_refundable', $feeCategory->is_refundable ?? false) ? 'checked' : '' }}>

                    <label class="form-check-label fw-semibold"
                           for="is_refundable">

                        Refundable Fee

                    </label>

                </div>

                <small class="text-muted">

                    Students can request refunds for this category.

                </small>

            </div>

        </div>

    </div>

    <div class="col-md-6 mb-4">

        <div class="card border">

            <div class="card-body">

                <div class="form-check">

                    <input type="checkbox"
                           name="is_optional"
                           value="1"
                           class="form-check-input"
                           id="is_optional"

                           {{ old('is_optional', $feeCategory->is_optional ?? false) ? 'checked' : '' }}>

                    <label class="form-check-label fw-semibold"
                           for="is_optional">

                        Optional Fee

                    </label>

                </div>

                <small class="text-muted">

                    Students may choose whether to pay this fee.

                </small>

            </div>

        </div>

    </div>

    <div class="col-md-12 mb-4">

        <label class="form-label fw-semibold">
            Description
        </label>

        <textarea name="description"
                  rows="5"
                  class="form-control"
                  placeholder="Enter category description...">{{ old('description', $feeCategory->description ?? '') }}</textarea>

    </div>

</div>
