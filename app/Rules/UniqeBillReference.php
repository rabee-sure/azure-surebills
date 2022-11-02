<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

use App\Models\Application;
use App\Models\Bill;

class UniqeBillReference implements Rule
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
        $application = Application::find(request()->application_id);

        $bill = Bill::where('reference_id', $value)->where('user_id', $application->user_id ?? null)->whereIn('status', ['paid', 'paid_cash', 'paid_bank_transfer', 'paid_machine'])->first();
        if($bill){
            return false;
        }else{
            return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'This bill paid before';
    }
}
