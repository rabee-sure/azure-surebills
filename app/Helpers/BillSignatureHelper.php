<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BillSignatureHelper
{
    /**
     * Generate a HMAC signature for a bill
     */
    public static function generateSignature($bill, $payTime)
    {
        // bill details for signing
        $billData = self::getBillData($bill, $payTime);
        
        // Get bill secret key
        $secretKey = config('payment.bill_form_signature_secret_key');

        // Convert bill data to a string
        $dataString = json_encode($billData, JSON_UNESCAPED_SLASHES);

        // Generate HMAC-SHA256 signature
        return base64_encode(hash_hmac('sha256', $dataString, $secretKey));
    }

    /**
     * Validate the HMAC signature of a bill
     */
    public static function validateSignature($bill, $payTime, $billSignature)
    {
        // Generate the expected signature
        $expectedSignature = self::generateSignature($bill, $payTime);

        // Securely compare signatures
        return hash_equals($expectedSignature, $billSignature);
    }

    /**
     * Get bill details for signing
     */
    private static function getBillData($bill, $payTime)
    {
        return [
            'bill_id'    => $bill->id,
            'bill_total' => $bill->total,
            'created_at' => $bill->created_at->toISOString(),
            'pay_time' => (string) $payTime
        ];
    }
}
