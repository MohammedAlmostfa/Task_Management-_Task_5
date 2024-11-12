<?php

namespace App\Http\Requests;

use App\Rules\checkrole;
use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

use Illuminate\Contracts\Validation\Validator;

class crTaskFormRquest extends FormRequest
{

    protected function failedValidation(Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, response()->json($validator->errors(), 422));
    }
    public function prepareForValidation()
    {

        $this->merge([
             'due_date' => Carbon::parse($this->input('due_date'))->format('Y-m-d'),
        ]);

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
            'title' => 'required|string|min:5|max:25',
            'description' => 'required|string|min:25|max:100',
            'priority' => 'required|string',
            'due_date' => 'required|date|after:today',
            'status' => 'nullable|string',
            'assigned_to' => ['nullable', 'integer', new \App\Rules\checkrole],
        ];
    }

    public function attributes()
    {
        return [
            'title' => 'العنوان',
            'description' => 'الوصف',
            'priority' => 'المستوى',
            'due_date' => 'تاريخ الانهاء',
            'status' => 'الحالة',
            'assigned_to' => 'مسندة الى',

        ];
    }
    public function messages()
    {
        return [
            'required' => 'حقل :attribute مطلوب',
            'string' => 'حقل :attribute من نوع نصي',
            'min' => 'يجب أن يكون :attribute أكبر من :min حروف',
            'max' => 'يجب أن يكون :attribute أقل من :max حروف',
            'date' => 'حقل :attribute يجب أن يكون تاريخًا صالحًا',
            'integer' => 'حقل :attribute يجب أن يكون رقمًا صحيحًا',
            'exists' => 'حقل :attribute غير موجود في قاعدة البيانات',
           'after' => 'يجب أن يكون :attribute بعد :date.',
        ];
    }
}
