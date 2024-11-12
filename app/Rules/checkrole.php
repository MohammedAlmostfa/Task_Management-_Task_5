<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class checkrole implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $exists = DB::table('users')
                   ->where('id', $value)
                   ->where('role', 'user')
                   ->exists();

        if (!$exists) {
            $fail('لا يمكنك إسناد مهمة له');
        }
    }
}
