<?php
  
namespace App\Rules;
  
use App\Channel;
use App\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;
  
class MinValueOfChannel implements Rule
{
    protected $min;

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if(null !== request()->get('viaResourceId')){
            $this->min = Channel::find(request()->get('viaResourceId'))->$attribute;
            return $value > $this->min;
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
        return __(':attribute must be greater than :number', ['number'=> $this->min]);
    }
}