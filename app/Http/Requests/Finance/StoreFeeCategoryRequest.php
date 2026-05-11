<?php

namespace App\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeeCategoryRequest extends FormRequest
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

            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'code' => [

                'required',
                'string',
                'max:100',

                'unique:fee_categories,code'
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
