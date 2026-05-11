<div class="row">

    <div class="col-md-6 mb-3">

        <label class="form-label">
            Structure Title
        </label>

        <input type="text"
               name="title"
               class="form-control"
               value="{{ old('title', $structure->title ?? '') }}"
               required>

    </div>

    <div class="col-md-6 mb-3">

        <label class="form-label">
            Class
        </label>

        <select name="class_id"
                class="form-select"
                required>

            <option value="">
                Select Class
            </option>

            @foreach($classes ?? [] as $class)

                <option value="{{ $class->id }}">
                    {{ $class->name }}
                </option>

            @endforeach

        </select>

    </div>

    <div class="col-md-6 mb-3">

        <label class="form-label">
            Installment Type
        </label>

        <select name="installment_type"
                class="form-select">

            <option value="monthly">Monthly</option>
            <option value="quarterly">Quarterly</option>
            <option value="half_yearly">Half Yearly</option>
            <option value="yearly">Yearly</option>

        </select>

    </div>

    <div class="col-md-6 mb-3">

        <label class="form-label">
            Due Type
        </label>

        <select name="due_type"
                class="form-select">

            <option value="fixed_date">Fixed Date</option>
            <option value="monthly_cycle">Monthly Cycle</option>
            <option value="custom">Custom</option>

        </select>

    </div>

    <div class="col-md-12 mb-3">

        <label class="form-label">
            Description
        </label>

        <textarea name="description"
                  rows="4"
                  class="form-control"></textarea>

    </div>

</div>
