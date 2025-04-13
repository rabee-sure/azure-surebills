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

        $result = Http::withHeaders([
            'email' => config('sure_connect.email'),
            'apiKey' => config('sure_connect.apiKey'),
        ])->post('https://api.sureconnects.com/api/Message/SendMessage', [
            'sender' => config('sure_connect.sender'),
            'message' => $message,
            'messageInfos' => [
                [
                    'distination' => '"' . $mobile . '",',
                    'customMessage' => $message
                ]
            ],
            'filterRepeatedNumbers' => false,
            'requestDeliveryReport' => false
        ]);
        
        $response = json_decode($result, true);

        $this->logResult('sms-logs', $response);

        return $response;
    }
    
    private function logResult($fileName, $result)
    {
        Log::build(['driver' => 'single', 'path' => storage_path('logs/' . $fileName . '.log'), 'level' => 'debug'])->error($result);
    }
}