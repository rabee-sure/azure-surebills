<?php
  
namespace App\Rules;
  
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;
  
class EmailChannel implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return true;
        $user_ids = request()->route('channel')->applications->pluck('user_id')->toArray();
        return !in_array(User::whereEmail($value)->first()->id?? null, $user_ids);
    }
   
    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('client should not have multiple applications under the same channel');
    }
}