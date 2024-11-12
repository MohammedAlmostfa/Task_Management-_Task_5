<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class TaskRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, response()->json($validator->errors(), 422));
    }
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    //prepar for validation
    public function prepareForValidation()
    {// organize  the date
        if ($this->isMethod('post')) {
            $this->merge([
                 'due_date' => Carbon::parse($this->input('due_date'))->format('Y-m-d'),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {// for store useer an register
        if ($this->isMethod('post')) {

            return [
             'title' => 'required|string|min:5|max:25',
             'description' => 'required|string|min:25|max:100',
             'priority' => 'required|string',
             'due_date' => 'required|date|after:today',
             'status' => 'nullable|string',
             'assigned_to' => 'nullable|integer|exists:users,id',

     ];

        }
        // for update user
        elseif($this->isMethod('put') || $this->isMethod('patch')) {
            return [
              'title' => 'nullable|string|min:5|max:25',
              'description' => 'nullable|string|min:25|max:100',
              'priority' => 'nullable|string',
                'due_date' => 'nullable|date|after:today',
              'status' => 'nullable|string',
              'assigned_to' => 'nullable|integer|exists:users,id',
     ];

        } else {
            return [
           'status' => 'nullable|string',
            'priority' => 'nullable|string',];
        }


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
