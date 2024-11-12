<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;

use Illuminate\Foundation\Http\FormRequest;

class ratingformrequest extends FormRequest
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
           'rating' => 'required|integer|between:1,5',

        ];
    }
    public function messages()
    {
        return [
            'required' => 'حقل :attribute مطلوب',
            'integer' => 'يجب أن يكون حقل :attribute من نوع رقم',
            'between'=>'يجب ان يكون حق ال :attribute بين ال 1 و5',

        ];
    }
    public function attributes()
    {
        return [
            'rating' => 'التقييم',

        ];
    }

}
