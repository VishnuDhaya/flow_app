<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class UEZMCustIDRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return preg_match("/^[0-9]{7,9}[/][\d]$/", $value) || preg_match("^[0-9]{7,9}$", $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The data provider cust ID is not valid';
    }
}
