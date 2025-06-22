<?php

namespace App\Rules;

use App\Models\Bill;
use Illuminate\Contracts\Validation\Rule;

class BillTotalValidation implements Rule
{
    const MAX_TOTAL_AMOUNT = 150000;

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
            return ($item['price'] ?? 0) * ($item['quantity'] ?? 0);
        });

        if(request()->has('add_discount'))
        {
            if(request()->discount_type == 'fixed')
                $this->total -= request()->discount_value;
            else if(request()->discount_type == 'percentage')
                $this->total -= ($this->total * request()->discount_value) / 100;
        }

        $addTax = false;
        $taxValue = null;

        if(request()->bill != null){
            $mainBill = Bill::find(request()->bill);
            $addTax = $mainBill->add_tax;
            $taxValue = $mainBill->tax_value;
        }else{
            if(request()->has('add_tax') && (request()->add_tax == true || request()->add_tax != null))
            {
                $addTax = true;
                $taxValue = request()->tax_value;
            }
        }
        
        if($addTax)
            $this->total += ($this->total * $taxValue) / 100;

        return !($this->total < 2 || $this->total > self::MAX_TOTAL_AMOUNT);
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
        else if($this->total > self::MAX_TOTAL_AMOUNT)
            return __("Invoice total is more than :amount SAR", ['amount' => self::MAX_TOTAL_AMOUNT]);
    }
}
