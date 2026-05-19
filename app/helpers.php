<?php

use chillerlan\QRCode\QRCode;
use Illuminate\Support\Facades\Storage;
use Salla\ZATCA\GenerateQrCode;
use Salla\ZATCA\Tags\InvoiceDate;
use Salla\ZATCA\Tags\InvoiceTaxAmount;
use Salla\ZATCA\Tags\InvoiceTotalAmount;
use Salla\ZATCA\Tags\Seller;
use Salla\ZATCA\Tags\TaxNumber;
use Illuminate\Support\Str; 

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

if (! function_exists('oci_storage_enabled')) {
    /**
     * Whether OCI Object Storage is enabled via OCI_ENABLED.
     */
    function oci_storage_enabled(): bool
    {
        return (bool) config('oci.enabled', false);
    }
}

if (! function_exists('public_storage_url')) {
    /**
     * Resolve a public URL for a file on the "public" disk (local or OCI).
     *
     * Local mode uses /media/{path} (no storage:link). OCI mode uses bucket URLs.
     */
    function public_storage_url(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        $path = ltrim($path, '/');

        if (! Storage::disk('public')->exists($path)) {
            return url($path);
        }

        return Storage::disk('public')->url($path);
    }
}

if (! function_exists('merchant_logo_disk_path')) {
    /**
     * Relative path on the "public" disk for merchant business logos.
     */
    function merchant_logo_disk_path(): string
    {
        return 'logos';
    }
}

if (! function_exists('ensure_merchant_logo_directory')) {
    /**
     * Ensure logos/ exists on the public disk (writable by the web server).
     */
    function ensure_merchant_logo_directory(): void
    {
        $disk = Storage::disk('public');
        $dir = merchant_logo_disk_path();

        if (! $disk->exists($dir)) {
            $disk->makeDirectory($dir);
        }
    }
}

if (! function_exists('store_merchant_logo')) {
    /**
     * Store a merchant business logo on the public disk under logos/.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @return string  e.g. "logos/1234567890_42.png"
     */
    function store_merchant_logo($file, int $userId): string
    {
        ensure_merchant_logo_directory();

        $extension = $file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'png';
        $filename = time().'_'.$userId.'.'.strtolower($extension);

        return Storage::disk('public')->putFileAs(
            merchant_logo_disk_path(),
            $file,
            $filename
        );
    }
}

if (! function_exists('delete_merchant_logo')) {
    /**
     * Remove a merchant logo file from disk (public disk and legacy public/uploads).
     */
    function delete_merchant_logo(?string $path): void
    {
        if (empty($path)) {
            return;
        }

        $path = ltrim($path, '/');

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        if (strpos($path, 'uploads/') === 0) {
            $legacy = public_path($path);
            if (is_file($legacy)) {
                @unlink($legacy);
            }
        }
    }
}

if (! function_exists('auth_user_logo_path')) {
    /**
     * Logo path for the authenticated merchant (owner or main store user).
     */
    function auth_user_logo_path(): ?string
    {
        $user = auth()->user();

        if (! $user) {
            return null;
        }

        if ($user->mainStoreUser && $user->mainStoreUser->logo) {
            return $user->mainStoreUser->logo;
        }

        return $user->logo ?: null;
    }
}

if (! function_exists('merchant_logo_url')) {
    /**
     * Resolve the display URL for a merchant business logo.
     *
     * Handles:
     * - logos/* on the public disk (Nova + unified uploads)
     * - legacy uploads/* under public/uploads
     * - legacy root-level hashes on the public disk (pre-path Nova uploads)
     */
    function merchant_logo_url(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        $path = ltrim($path, '/');

        if (Storage::disk('public')->exists($path)) {
            return public_storage_url($path);
        }

        if (strpos($path, 'uploads/') === 0) {
            if (is_file(public_path($path))) {
                return url($path);
            }

            $migrated = merchant_logo_disk_path().'/'.basename($path);
            if (Storage::disk('public')->exists($migrated)) {
                return public_storage_url($migrated);
            }

            return url($path);
        }

        return url($path);
    }
}

if (! function_exists('bills_background_disk_path')) {
    function bills_background_disk_path(): string
    {
        return 'bills_backgrounds';
    }
}

if (! function_exists('ensure_bills_background_directory')) {
    function ensure_bills_background_directory(): void
    {
        $disk = Storage::disk('public');
        $dir = bills_background_disk_path();

        if (! $disk->exists($dir)) {
            $disk->makeDirectory($dir);
        }
    }
}

if (! function_exists('store_bill_background_image')) {
    /**
     * Store bill UI background image on the public disk (OCI when enabled).
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @return string  e.g. "bills_backgrounds/1779210942_240.png"
     */
    function store_bill_background_image($file, int $userId): string
    {
        ensure_bills_background_directory();

        $extension = $file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'png';
        $filename = time().'_'.$userId.'.'.strtolower($extension);

        return Storage::disk('public')->putFileAs(
            bills_background_disk_path(),
            $file,
            $filename
        );
    }
}

if (! function_exists('delete_bill_background_image')) {
    function delete_bill_background_image(?string $path): void
    {
        if (empty($path)) {
            return;
        }

        $path = ltrim($path, '/');

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        if (strpos($path, 'uploads/') === 0 && is_file(public_path($path))) {
            @unlink(public_path($path));
        }
    }
}

if (! function_exists('media_route_url')) {
    /**
     * Same-origin URL to stream a public-disk file (works with OCI + local fallback).
     *
     * Use in HTML/CSS on payment pages instead of direct OCI signed URLs so:
     * - CSP img-src 'self' allows the request
     * - Blade does not break AWS signatures by escaping & to &amp;
     */
    function media_route_url(string $path): string
    {
        $path = ltrim($path, '/');

        return route('media.show', ['path' => $path], true);
    }
}

if (! function_exists('bill_background_image_url')) {
    /**
     * Resolve URL for bill payment page background image.
     *
     * Uses /media/ proxy when the file is on the public disk (OCI or local).
     * Legacy uploads/bills_backgrounds/ under public/ still use direct url().
     */
    function bill_background_image_url(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        $path = ltrim($path, '/');

        if (Storage::disk('public')->exists($path)) {
            return media_route_url($path);
        }

        if (strpos($path, 'uploads/bills_backgrounds/') === 0) {
            if (is_file(public_path($path))) {
                return url($path);
            }

            $migrated = bills_background_disk_path().'/'.basename($path);
            if (Storage::disk('public')->exists($migrated)) {
                return media_route_url($migrated);
            }

            return url($path);
        }

        if (strpos($path, 'uploads/') === 0 && is_file(public_path($path))) {
            return url($path);
        }

        return url($path);
    }
}

if (! function_exists('public_storage_path')) {
    /**
     * Local filesystem path for the public disk when available (local fallback).
     *
     * Code that uses storage_path('app/public/...') for reads should prefer
     * Storage::disk('public') when possible. This helper returns a local path
     * only when the file exists on the local fallback disk.
     */
    function public_storage_path(string $path = ''): string
    {
        $path = ltrim($path, '/');

        if (oci_storage_enabled() && $path !== '') {
            $local = \Illuminate\Support\Facades\Storage::disk('public-local');
            if ($local->exists($path)) {
                return $local->path($path);
            }
        }

        return storage_path('app/public'.($path !== '' ? '/'.$path : ''));
    }
}
