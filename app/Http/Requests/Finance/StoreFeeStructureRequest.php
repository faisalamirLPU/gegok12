<?php

namespace App\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeeStructureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'school_id' => ['required'],
            'academic_year_id' => ['required'],

            'class_id' => [
                'nullable',
                'exists:standards,id',
            ],
            'section_id' => ['nullable'],

            'title' => ['required', 'string'],

            'installment_type' => ['required'],

            'due_type' => ['required'],

            'items' => ['required', 'array'],

            'items.*.fee_category_id' => ['required'],
            'items.*.amount' => ['required', 'numeric', 'min:0'],
        ];
    }
    /*
    |--------------------------------------------------------------------------
    | Conditional Validation
    |--------------------------------------------------------------------------
    */

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $isGlobal = $this->has('is_global');

            if (!$isGlobal && empty($this->class_id)) {

                $validator->errors()->add(
                    'class_id',
                    'The class id field is required.'
                );
            }
        });
    }
}
