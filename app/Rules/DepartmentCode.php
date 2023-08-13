<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DepartmentCode implements ValidationRule
{
    protected $departments;

    public function __construct()
    {
        $this->departments = array_column(config('region'), 'dep_name', 'num_dep');
    }

    /**
     * Run the validation rule.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @param  Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!isset($this->departments[$value])) {
            $fail('Le code du département n\'est pas valide.');
        }
    }
}
