<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidateUploadFile implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    private $allowedExtensions = [];
    private $maxSize;
    private $sizeInMega;
    private $uploadedFile;

    public function __construct($allowedExtensions = [], $maxSize = 4096)
    {
        $this->allowedExtensions = $allowedExtensions;
        $this->maxSize = $maxSize;
        $this->sizeInMega = round($this->maxSize/1024);
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
        $notValidFlag = 0;

        if(is_array($value))
        {
            foreach($value as $file)
            {
                if(is_file($file))
                {
                    $validate = $this->validateFile($file);
                    if(!$validate)
                    {
                        $notValidFlag = 1;
                        break;
                    }
                }
            }
            if($notValidFlag)
            {
                return false;
            }
            else
            {
                return true;
            }
        }
        else
        {
            if(is_file($value))
            {
                return $this->validateFile($value);
            }
            else
            {
                return true;
            }
        }
    }

    private function validateFile($file)
    {
        $this->uploadedFile = $file;

        if(round($this->uploadedFile->getSize()/1024, 0) > $this->maxSize || !in_array(strtolower($this->uploadedFile->getClientOriginalExtension()), $this->allowedExtensions))
        {
            return false;
        }
        else
        {
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
        if(round($this->uploadedFile->getSize()/1024, 0) > $this->maxSize)
        {
            return trans('file max size should be less than or equal', ['value' => $this->sizeInMega]);
        }
        else if(!in_array(strtolower($this->uploadedFile->getClientOriginalExtension()), $this->allowedExtensions))
        {
            return trans('file extension should be in', ['value' => implode(', ', $this->allowedExtensions)]);
        }
    }
}
