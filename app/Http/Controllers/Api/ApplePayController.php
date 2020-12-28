<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApplePayController extends Controller
{
    public function validateMerchant(Request $request, $url)
    {
        $ch = curl_init();
        $data = '{"merchantIdentifier":"merchant.bills.surepay.mastercard.applepay.sandbox", "domainName":"bills.surepay.sa", "displayName":"SureBills"}';
        curl_setopt($ch, CURLOPT_URL, urldecode($url));
        curl_setopt($ch, CURLOPT_SSLCERT, base_path('app/Payment/Drivers/MasterCardApplePay/ApplePay.crt.pem'));
        curl_setopt($ch, CURLOPT_SSLKEY, base_path('app/Payment/Drivers/MasterCardApplePay/ApplePay.key.pem'));
        curl_setopt($ch, CURLOPT_SSLKEYPASSWD, '7t2R8sYhc3Tz');
        //curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS);
        //curl_setopt($ch, CURLOPT_SSLVERSION, 'CURL_SSLVERSION_TLSv1_2');
        //curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'rsa_aes_128_gcm_sha_256,ecdhe_rsa_aes_128_gcm_sha_256');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        if(curl_exec($ch) === false)
        {
            echo json_encode('{"curlError":"' . curl_error($ch) . '"}');
        }

        // close cURL resource, and free up system resources
        curl_close($ch);
    }
}
