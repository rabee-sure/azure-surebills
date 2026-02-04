<?php

namespace App\Services;

use App\Abstracts\SMSAbstract;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SureConnectService extends SMSAbstract

{
    public function __construct()
    {
        
    }

    public function sendSMS($mobile, $message){
        $mobile = (int) $mobile;

        $url = 'https://api.sureconnects.com/api/NewMessage/SendMessage';

        $headers = [
            'email'    => config('sms.sure-connect.email'),
            'apiKey'   => config('sms.sure-connect.apiKey'),
            'Content-Type' => 'application/json',
        ];

        $data = [
            'senderId' => config('sms.sure-connect.sender'),
            'messageBody' => $message,
            'messageType' => 3,
            'Recipients' => [$mobile],
        ];

        $result = Http::withHeaders($headers)->post($url, $data);
        
        $response = json_decode($result, true);

        $this->logResult('sms-logs', $response);

        return $response;
    }
    
    private function logResult($fileName, $result)
    {
        Log::build(['driver' => 'single', 'path' => storage_path('logs/' . $fileName . '.log'), 'level' => 'debug'])->error($result);
    }
}