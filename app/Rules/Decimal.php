<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Decimal implements Rule
{
    protected $parameters;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->parameters = func_get_args();
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
        return preg_match("/^[0-9]{1,{$this->parameters[0]}}(\.[0-9]{1,{$this->parameters[1]}})$/", $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('The :attribute must be an appropriately formatted decimal e.g. ') . $this->example();
    }

    public function example()
    {
        return mt_rand(1, (int) str_repeat('9', $this->parameters[0])) .
               '.' . 
               mt_rand(1, (int) str_repeat('9', $this->parameters[1]));
    }
}
