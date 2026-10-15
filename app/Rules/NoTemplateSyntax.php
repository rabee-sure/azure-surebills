<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NoTemplateSyntax implements Rule
{
    /**
     * Stored text is printed inside pages Vue compiles.
     * Double braces are Vue template syntax, so reject them before they are saved.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!is_string($value)) {
            return true;
        }

        return strpos($value, '{{') === false && strpos($value, '}}') === false;
    }

    /**
     * @return string
     */
    public function message()
    {
        return __('This value cannot contain {{ or }}.');
    }
}
