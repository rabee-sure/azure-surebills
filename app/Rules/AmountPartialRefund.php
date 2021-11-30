<?php

namespace App\Rules;

use App\Models\Bill;
use Illuminate\Contracts\Validation\Rule;

class AmountPartialRefund implements Rule
{
    protected $id;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->id = func_get_args()[0];
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
        $bill = Bill::find($this->id);

        return $value < $bill->total;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('The :attribute must be less than Bill Amount');
    }
}
