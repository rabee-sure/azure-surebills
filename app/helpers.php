<?php

use Laravel\Nova\Actions\Action;
use chillerlan\QRCode\QRCode;
use Salla\ZATCA\GenerateQrCode;
use Salla\ZATCA\Tags\InvoiceDate;
use Salla\ZATCA\Tags\InvoiceTaxAmount;
use Salla\ZATCA\Tags\InvoiceTotalAmount;
use Salla\ZATCA\Tags\Seller;
use Salla\ZATCA\Tags\TaxNumber;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Spatie\Valuestore\Valuestore;

if (!function_exists('getMastercardError')) {
    function getMastercardError($response)
    {
        if (isset($response['error']) && isset($response['error']['explanation'])) {
            return $response['error']['explanation'];
        }

        if (isset($response['response']) && isset($response['response']['gatewayCode'])) {
            return $response['response']['gatewayCode'];
        }
    }
}
 
if (!function_exists('mastercard_simulation_enabled')) {
    /**
     * Check if Mastercard full payment-cycle simulation is enabled.
     *
     * This is hard-disabled in production for safety and can be toggled
     * per environment using the MASTERCARD_PAYMENT_SIMULATION env flag.
     */
    function mastercard_simulation_enabled(): bool
    {
        return !app()->environment('production')
            && (bool) config('mastercard.payment_simulation', false);
    }
}

if (!function_exists('getBanks')) {
    function getBanks()
    {
        $banks = [
            [
                "id" => 1,
                "en" => "National Bank of Abu Dhabi",
                "ar" => "بنك أبوظبي الوطني",
            ],
            [
                "id" => 2,
                "en" => "MUFG EMEA",
                "ar" => "MUFG EMEA",
            ],
            [
                "id" => 3,
                "en" => "Industrial and Commercial Bank of China Ltd",
                "ar" => "البنك الصناعي والتجاري الصيني المحدود",
            ],
            [
                "id" => 4,
                "en" => "Ziraat Bankası",
                "ar" => "بنك زراعات التركي",
            ],
            [
                "id" => 5,
                "en" => "National Bank of Pakistan",
                "ar" => "البنك الوطني الباكستاني",
            ],
            [
                "id" => 6,
                "en" => "J.P. Morgan Bank",
                "ar" => "جي بي مورغان تشيس",
            ],
            [
                "id" => 7,
                "en" => "BNP Paribas",
                "ar" => "بي إن بي باريبا",
            ],
            [
                "id" => 8,
                "en" => "Deutsche Bank",
                "ar" => "دويتشه بنك",
            ],
            [
                "id" => 9,
                "en" => "Bank Muscat",
                "ar" => "بنك مسقط",
            ],
            [
                "id" => 10,
                "en" => "National Bank of Kuwait",
                "ar" => "بنك الكويت الوطني",
            ],
            [
                "id" => 11,
                "en" => "National Bank of Bahrain",
                "ar" => "بنك البحرين الوطني",
            ],
            [
                "id" => 12,
                "en" => "Emirates NBD",
                "ar" => "بنك الإمارات دبي الوطني",
            ],
            [
                "id" => 13,
                "en" => "Gulf International Bank",
                "ar" => "بنك الخليج الدولي",
            ],
            [
                "id" => 14,
                "en" => "Alinma Bank",
                "ar" => "مصرف الإنماء",
            ],
            [
                "id" => 15,
                "en" => "Al-Rajhi Bank",
                "ar" => "مصرف الراجحي",
            ],
            [
                "id" => 16,
                "en" => "Samba Financial Group",
                "ar" => "مجموعة سامبا المالية",
            ],
            [
                "id" => 17,
                "en" => "Riyad Bank",
                "ar" => "بنك الرياض",
            ],
            [
                "id" => 18,
                "en" => "Bank AlJazira",
                "ar" => "بنك الجزيرة",
            ],
            [
                "id" => 19,
                "en" => "Al Bilad Bank",
                "ar" => "بنك البلاد",
            ],
            [
                "id" => 20,
                "en" => "Arab National Bank",
                "ar" => "البنك العربي الوطني",
            ],
            [
                "id" => 21,
                "en" => "The Saudi Investment Bank",
                "ar" => "البنك السعودي للاستثمار",
            ],
            [
                "id" => 22,
                "en" => "Alawwal Bank",
                "ar" => "البنك الأول",
            ],
            [
                "id" => 23,
                "en" => "Banque Saudi Fransi",
                "ar" => "البنك السعودي الفرنسي",
            ],
            [
                "id" => 24,
                "en" => "British Saudi Bank",
                "ar" => "بنك ساب",
            ],
            [
                "id" => 25,
                "en" => "National Commercial Bank",
                "ar" => "البنك الأهلي التجاري",
            ],
        ];
        return $banks;
    }
}


if (!function_exists('round2')) {
    function round2($number)
    {
        $resualt = round($number, 2);
        return $resualt;
        // return $resualt > 0 ? $resualt:0;
    }
}

if (!function_exists('floorp')) {
    function floorp($val, $precision)
    {
        $mult = pow(10, $precision); // Can be cached in lookup table
        return floor($val * $mult) / $mult;
    }
}


if (!function_exists('fact_number')) {
    function fact_number($number)
    {
        if($number == -0)
            return 0;
        else
            return $number;
    }
}

if(!function_exists('generateQRcode')){
    function generateQRcode($bill, $src = null){

        $displayQRCodeAsBase64 = GenerateQrCode::fromArray([
            new Seller($bill->user->business_name_ar), // seller name
            new TaxNumber($bill->user->vat_registration_number), // seller tax number
            new InvoiceDate($bill->paid_at), // invoice date as Zulu ISO8601 @see https://en.wikipedia.org/wiki/ISO_8601
            new InvoiceTotalAmount($bill->total), // invoice total amount
            new InvoiceTaxAmount($bill->tax_value) // invoice tax amount
            // TODO :: Support others tags
        ])->render();

        $qr = $src ? $displayQRCodeAsBase64 : '<img src="'.$displayQRCodeAsBase64.'" alt="QR Code" />';
        return $qr;
    }
}

if(!function_exists('generateUrlQRcode')){
    function generateUrlQRcode($url){
        $qr_code = (new QRCode)->render($url);
        return $qr_code;
    }
}

if(!function_exists('monthsCounter')){
    function monthsCounter($date_start, $date_to){
        $ts1 = strtotime($date_start);
        $ts2 = strtotime($date_to);

        $year1 = date('Y', $ts1);
        $year2 = date('Y', $ts2);

        $month1 = date('m', $ts1);
        $month2 = date('m', $ts2);

        $diff = (($year2 - $year1) * 12) + ($month2 - $month1);

       return $diff;
    }
}

if(!function_exists('generateSecureOTP')){
    function generateSecureOTP() {
        $randomBytes = random_bytes(2); // 2 bytes = 16 bits, enough for 0-9999
        $randomNumber = unpack('n', $randomBytes)[1] % 10000;
        return Str::padLeft($randomNumber, 4, '0');
    }
}

if (!function_exists('ociFile')) {
    function ociFile($path, $minutes = 10)
    {
        if (!$path) {
            return asset('images/no-image.jpg');
        }

        try {
            return Storage::disk('oci')
                ->temporaryUrl($path, now()->addMinutes($minutes));
        } catch (\Throwable $e) {
            return asset('images/no-image.jpg');
        }
    }
}

if (!function_exists('ociDownload')) {
    function ociDownload($path, $minutes = 5)
    {
        return redirect(
            Storage::disk('oci')
                ->temporaryUrl($path, now()->addMinutes($minutes))
        );
    }
}

if (!function_exists('ociReport')) {
    function ociReport($name, $id, $minutes = 5)
    {
        $path = "reports/{$name}/{$name}_{$id}.xlsx";

        try {
            return Storage::disk('oci')
                ->temporaryUrl($path, now()->addMinutes($minutes));
        } catch (\Throwable $e) {
            return null;
        }
    }
}


if (!function_exists('getSettings')){
    function getSettings()
    {
        return storage_path('app/nova-settings.json');

    }
}


