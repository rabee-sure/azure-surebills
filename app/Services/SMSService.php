<?php

namespace App\Services;

class SMSService

{
    protected $smsService;
    
    public function __construct()
    {
        if(config('sms.provider') == 'yamamah'){
            $smsService = new YamamahService();
        }elseif(config('sms.provider') == 'sure_connect'){
            $smsService = new SureConnectService();
        }
    }

    public function sendSMS($mobile, $message){
        $response = $this->smsService->sendSMS($mobile, $message);
        return $response;
    }
}