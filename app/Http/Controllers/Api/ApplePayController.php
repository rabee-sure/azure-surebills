<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApplePayController extends Controller
{
    public function validateMerchant(Request $request)
    {
        $validationUrl = "https://apple-pay-gateway-cert.apple.com/paymentservices/startSession";
        // JSON Payload 
        $validationPayload = '{"merchantIdentifier": "merchant.bills.surepay.mastercard.applepay.sandbox","domainName": "sure-bills.sure-lab.com","displayName":"SureBills"}';
        try {
            //setting the curl parameters.
            $ch = curl_init();
            if (FALSE === $ch)
                throw new Exception('failed to initialize');
            curl_setopt($ch, CURLOPT_URL, $validationUrl);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $validationPayload);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
            // The following two curl SSL options are set to "false" for ease of development/debug purposes only.
            // Any code used in production should either remove these lines or set them to the appropriate
            // values to properly use secure connections for PCI-DSS compliance.
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    //for production, set value to true or 1
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);    //for production, set value to 2
            curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
            curl_setopt($ch, CURLOPT_SSLCERT, 'https://sure-bills.sure-lab.com/certs/apple-pay-cert.pem');
            curl_setopt($ch, CURLOPT_SSLCERTPASSWD, '');
            curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false );        
            $content = curl_exec($ch);
            if (FALSE === $content) {
                dd(curl_error($ch));
                    throw new Exception(curl_error($ch), curl_errno($ch));
            }
            curl_close($ch);
            dd($content);
            // $content is the Apple Response, it should be a merchant session object
            // but may need to do some manipulation here
        
        } catch(Exception $e) {
            trigger_error(sprintf('Curl failed with error #%d: %s', $e->getCode(), $e->getMessage()), E_USER_ERROR);
        }
        dd($request->all());
    }
}
