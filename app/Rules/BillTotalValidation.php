<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class BillTotalValidation implements Rule
{
    private $total;
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
        $this->total = collect(request()->items)->sum(function ($item) {
            return $item['price'] ?? 0 * $item['quantity'] ?? 0;
        });

        if(request()->has('add_discount'))
        {
            if(request()->discount_type == 'fixed')
                $this->total -= request()->discount_value;
            else if(request()->discount_type == 'percentage')
                $this->total -= ($this->total * request()->discount_value) / 100;
        }

        return ($this->total > 2 || $this->total < 14000);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if($this->total < 2)
            return __('Invoice total is less than 2 SAR');
        else if($this->total > 14000)
            return __("Invoice total is more than 14000 SAR");
    }
}
