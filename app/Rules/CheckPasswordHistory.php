<?php

namespace App\Rules;

use App\Models\Admin;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class CheckPasswordHistory implements Rule
{
    private $email;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($email)
    {
        $this->email = $email;
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
        $passworHistory = true;
        $user = Admin::where('email', $this->email)->first();
        $lastPasswords = $user->passwordsHistory()->take(10)->orderBy('created_at', 'Desc')->pluck('password')->toArray();
        foreach($lastPasswords as $hashedPassword){
            if(Hash::check($value, $hashedPassword)) {
                $passworHistory = false;
                break;
            }
        }
        return $passworHistory;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'This password used before for you';
    }
}
