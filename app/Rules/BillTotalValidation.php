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
        $this->total = array_sum(collect(request()->items)->map(function ($item){
            return $item['price']??0 * $item['quantity']??0;
        })->toArray());

        if(request()->has('add_discount'))
        {
            if(request()->discount_type == 'fixed')
            {
                $this->total = $this->total - request()->discount_value;
            }
            else if(request()->discount_type == 'percentage')
            {
                $this->total -= ($this->total * request()->discount_value) / 100;
            }
        }

        if($this->total < 2 || $this->total > 14000)
        {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        if($this->total < 2)
        {
            return trans('invalid_min_total');
        }
        else if($this->total > 14000)
        {
            return trans('invalid_max_total');
        }
    }
}
