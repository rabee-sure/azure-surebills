<?php

namespace App\Abstracts;


abstract class SMSAbstract
{
    

    public function __construct() {
        
    }

    abstract public function sendSMS($mobile, $message);
    
}