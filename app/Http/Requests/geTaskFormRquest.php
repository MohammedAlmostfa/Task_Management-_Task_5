<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;

class geTaskFormRquest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, response()->json($validator->errors(), 422));
    }
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'nullable|string',
            'priority' => 'nullable|string',
        ];
    }
    public function attributes()
    {
        return [

            'priority' => 'المستوى',
            'status' => 'الحالة',


        ];
    }
    public function messages()
    {
        return [

            'string' => 'حقل :attribute من نوع نصي',

        ];
    }
}
