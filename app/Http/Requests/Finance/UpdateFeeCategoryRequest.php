<?php

namespace App\Http\Requests\Finance;

use Illuminate\Validation\Rule;

class UpdateFeeCategoryRequest extends StoreFeeCategoryRequest
{
    public function rules(): array
    {
        return [

            'school_id' => ['required'],

            'academic_year_id' => ['required'],

            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'code' => [

                'required',
                'string',
                'max:100',

                Rule::unique(
                    'fee_categories',
                    'code'
                )->ignore(
                    $this->fee_category
                )
            ],

            'frequency' => [
                'required'
            ],

            'description' => [
                'nullable'
            ],

            'is_refundable' => [
                'nullable',
                'boolean'
            ],

            'is_optional' => [
                'nullable',
                'boolean'
            ],

            'status' => [
                'nullable',
                'boolean'
            ],
        ];
    }
}
