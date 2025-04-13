<?php

namespace App\Services;

use App\Abstracts\SMSAbstract;
use Illuminate\Support\Facades\Log;

class YamamahService extends SMSAbstract

{
    public function __construct()
    {
        
    }

    public function sendSMS($mobile, $message){
        $mobile = (int) $mobile;

        $data = ["Tagname" => config('yamamah.sender'), "RecepientNumber" => "0" . $mobile, "Message" => $message, "Username" => config('yamamah.username'), "Password" => config('yamamah.password')];
        $payload = json_encode($data);
        $ch = curl_init('https://api.yamamah.com/SendSMS');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($payload)));
        $result = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($result, true);

        $this->logResult('sms-logs', $response);

        return $response;
    }
    
    private function logResult($fileName, $result)
    {
        Log::build(['driver' => 'single', 'path' => storage_path('logs/' . $fileName . '.log'), 'level' => 'debug'])->error($result);
    }
}