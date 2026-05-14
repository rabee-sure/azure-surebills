<?php

namespace App\Rules;

use App\Models\Bill;
use App\Models\Coupon;
use App\Models\CouponCode;
use App\Repositories\CouponRepository;
use Illuminate\Contracts\Validation\Rule;
use App\Services\Coupon\CouponService;
use App\Services\GetAuthUser;
use Illuminate\Support\Facades\Auth;

class BillTotalValidation implements Rule
{
    const MAX_TOTAL_AMOUNT = 150000;

    private $total;
    private CouponRepository $repository;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->repository = new CouponRepository();
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

        // Apply coupon discount
        if(request()->has('coupon_code') && request()->coupon_code != null){
            // Get auth user from api or web
            $authUser = GetAuthUser::authUser(request());
            
            // Try to find as reusable coupon first
            $coupon = $this->repository->findByCode(request()->coupon_code, $authUser->store_main_user_id ?? $authUser->id);
            
            if (!$coupon) {
                // Try to find as one-time code
                $couponCode = $this->repository->findCodeByCode(request()->coupon_code, $authUser->store_main_user_id ?? $authUser->id);
                
                if ($couponCode) {
                    $coupon = $couponCode->coupon;
                }
            }
            
            if($coupon){
                if($coupon->discount_type == 'fixed')
                    $this->total -= $coupon->discount_value;
                else if($coupon->discount_type == 'percentage')
                    $this->total -= ($this->total * $coupon->discount_value) / 100;
            }
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
            return 'less_than';
        else if($this->total > self::MAX_TOTAL_AMOUNT)
            return 'more_than';
    }
}
