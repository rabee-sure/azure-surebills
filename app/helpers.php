<?php

<<<<<<< HEAD
use App\Support\MerchantDocuments\MerchantDiskDocument;
use App\Support\Storage\ExportStoragePaths;
=======
use Laravel\Nova\Actions\Action;
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4
use chillerlan\QRCode\QRCode;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Salla\ZATCA\GenerateQrCode;
use Salla\ZATCA\Tags\InvoiceDate;
use Salla\ZATCA\Tags\InvoiceTaxAmount;
use Salla\ZATCA\Tags\InvoiceTotalAmount;
use Salla\ZATCA\Tags\Seller;
use Salla\ZATCA\Tags\TaxNumber;
<<<<<<< HEAD
=======
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4

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
<<<<<<< HEAD
 
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
=======
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4

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

<<<<<<< HEAD
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

if (! function_exists('oci_bucket_object_prefix')) {
    /**
     * Optional prefix for object keys under the OCI bucket (see OCI_BUCKET_PREFIX).
     * Returns e.g. "staging/" or "" when unset.
     */
    function oci_bucket_object_prefix(): string
    {
        $p = trim((string) config('oci.bucket_prefix', ''), '/');

        return $p === '' ? '' : $p.'/';
    }
}

if (! function_exists('merchant_logo_disk_path')) {
    /**
     * Relative path on the "public" disk for merchant business logos.
     *
     * Matches the canonical OCI tree:
     *   {OCI_BUCKET_PREFIX}/shared/merchants/logos/{filename}
     * Legacy keys may still use logos/ or shared/exports/merchants/logos/.
     */
    function merchant_logo_disk_path(): string
    {
        return oci_bucket_object_prefix().'shared/merchants/logos';
    }
}

if (! function_exists('ensure_merchant_logo_directory')) {
    /**
     * Ensure shared/merchants/logos/ (with optional bucket prefix) exists on the public disk.
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

if (! function_exists('merchant_business_documents_disk_path')) {
    /**
     * Root directory on the "public" disk for merchant business documents (admin-aligned).
     * {prefix}shared/merchants/business_documents
     */
    function merchant_business_documents_disk_path(): string
    {
        return ExportStoragePaths::merchantBusinessDocumentsRoot();
    }
}

if (! function_exists('merchant_bank_documents_disk_path')) {
    /**
     * Root directory on the "public" disk for merchant bank documents (admin-aligned).
     */
    function merchant_bank_documents_disk_path(): string
    {
        return ExportStoragePaths::merchantBankDocumentsRoot();
    }
}

if (! function_exists('merchant_admin_document_random_suffix')) {
    /**
     * Random lowercase alphanumeric suffix (Surebills admin MerchantService contract).
     */
    function merchant_admin_document_random_suffix(int $length = 6): string
    {
        $pool = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $max = strlen($pool) - 1;
        $out = '';
        for ($i = 0; $i < $length; $i++) {
            $out .= $pool[random_int(0, $max)];
        }

        return $out;
    }
}

if (! function_exists('merchant_document_unique_filename')) {
    /**
     * Unique stored filename: slug basename + "_" + 6 random alnum + extension (admin-aligned).
     */
    function merchant_document_unique_filename(UploadedFile $file): string
    {
        $origExt = $file->getClientOriginalExtension();
        $ext = $origExt !== '' && $origExt !== null
            ? strtolower((string) $origExt)
            : strtolower((string) ($file->guessExtension() ?: 'bin'));
        $base = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        if ($base === '') {
            $base = 'document';
        }
        if (strlen($base) > 80) {
            $base = substr($base, 0, 80);
        }

        return $base.'_'.merchant_admin_document_random_suffix(6).'.'.$ext;
    }
}

if (! function_exists('merchant_business_document_storage_candidates')) {
    /**
     * Relative keys to try on the public disk when attaching a business document (hidden form value).
     * Final layout: shared/merchants/business_documents/{userId}/{filename}; dropzone staging uses tmp/uploads.
     *
     * @return list<string>
     */
    function merchant_business_document_storage_candidates(string $input, int $userId): array
    {
        $input = ltrim($input, '/');
        if ($input === '' || $input === 'undefined') {
            return [];
        }
        $candidates = [];
        if (strpos($input, '/') !== false) {
            $candidates[] = $input;
        }
        $base = basename($input);
        $candidates[] = ExportStoragePaths::merchantBusinessDocumentUserPrefix($userId).'/'.$base;
        $candidates[] = 'tmp/uploads/'.$base;
        $candidates[] = merchant_business_documents_disk_path().'/'.$base;
        $candidates[] = merchant_business_documents_disk_path().'/'.$userId.'/'.$base;

        return array_values(array_unique(array_filter($candidates)));
    }
}

if (! function_exists('merchant_bank_document_storage_candidates')) {
    /**
     * @return list<string>
     */
    function merchant_bank_document_storage_candidates(string $input, int $userId): array
    {
        $input = ltrim($input, '/');
        if ($input === '' || $input === 'undefined') {
            return [];
        }
        $candidates = [];
        if (strpos($input, '/') !== false) {
            $candidates[] = $input;
        }
        $base = basename($input);
        $candidates[] = ExportStoragePaths::merchantBankDocumentUserPrefix($userId).'/'.$base;
        $candidates[] = 'tmp/uploads/'.$base;
        $candidates[] = merchant_bank_documents_disk_path().'/'.$base;
        $candidates[] = merchant_bank_documents_disk_path().'/'.$userId.'/'.$base;

        return array_values(array_unique(array_filter($candidates)));
    }
}

if (! function_exists('merchant_document_input_references_stored_file')) {
    /**
     * Whether a dropzone hidden-field value refers to an existing stored file (basename or full relative key).
     */
    function merchant_document_input_references_stored_file(string $input, string $storedFileName): bool
    {
        $input = trim(str_replace('\\', '/', $input));
        $storedFileName = trim(str_replace('\\', '/', $storedFileName));
        if ($input === '' || $storedFileName === '') {
            return false;
        }
        if ($input === $storedFileName) {
            return true;
        }

        return basename($input) === $storedFileName;
    }
}

if (! function_exists('merchant_document_input_references_media_file')) {
    /** @deprecated Use merchant_document_input_references_stored_file */
    function merchant_document_input_references_media_file(string $input, string $mediaFileName): bool
    {
        return merchant_document_input_references_stored_file($input, $mediaFileName);
    }
}

if (! function_exists('merchant_dropzone_staging_may_delete_after_import')) {
    /**
     * Whether a path may be removed after a successful import (tmp staging only).
     */
    function merchant_dropzone_staging_may_delete_after_import(string $relativePath): bool
    {
        $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');
        if ($relativePath === '' || strpos($relativePath, '..') !== false) {
            return false;
        }

        return strpos($relativePath, 'tmp/uploads/') === 0;
    }
}

if (! function_exists('merchant_merchant_document_sync_path_allowed')) {
    /**
     * Allowed sources during sync: tmp/uploads/{file} or final prefix/{userId}/{file}.
     */
    function merchant_merchant_document_sync_path_allowed(string $path, int $userId, string $collection): bool
    {
        if (! in_array($collection, ['business_documents', 'bank_documents'], true)) {
            return false;
        }
        $path = ltrim(str_replace('\\', '/', $path), '/');
        if ($path === '' || strpos($path, '..') !== false) {
            return false;
        }
        $prefix = $collection === 'business_documents'
            ? ExportStoragePaths::merchantBusinessDocumentUserPrefix($userId)
            : ExportStoragePaths::merchantBankDocumentUserPrefix($userId);
        if (strpos($path, $prefix.'/') === 0) {
            return true;
        }
        if (strpos($path, 'tmp/uploads/') === 0) {
            $rest = substr($path, strlen('tmp/uploads/'));

            return $rest !== '' && strpos($rest, '/') === false;
        }

        return false;
    }
}

if (! function_exists('merchant_disk_documents_collection')) {
    /**
     * Business or bank documents for a merchant user from the public disk (no Spatie).
     *
     * @return \Illuminate\Support\Collection<int, MerchantDiskDocument>
     */
    function merchant_disk_documents_collection(int $userId, string $collection)
    {
        if (! in_array($collection, ['business_documents', 'bank_documents'], true)) {
            return collect();
        }
        $prefix = $collection === 'business_documents'
            ? ExportStoragePaths::merchantBusinessDocumentUserPrefix($userId)
            : ExportStoragePaths::merchantBankDocumentUserPrefix($userId);
        $disk = Storage::disk('public');
        if (! $disk->exists($prefix)) {
            return collect();
        }
        $items = [];
        foreach ($disk->files($prefix) as $relativePath) {
            $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');
            $items[] = MerchantDiskDocument::fromRelativePath($relativePath, $collection);
        }
        usort($items, function (MerchantDiskDocument $a, MerchantDiskDocument $b) {
            return strcmp($a->file_name, $b->file_name);
        });

        return collect($items);
    }
}

if (! function_exists('merchant_dropzone_documents_payload')) {
    /**
     * JSON-safe rows for account dropzone (disk-backed merchant documents).
     *
     * @return list<array<string, mixed>>
     */
    function merchant_dropzone_documents_payload(int $userId, string $collection): array
    {
        return merchant_disk_documents_collection($userId, $collection)->map(function (MerchantDiskDocument $doc) {
            return [
                'id' => $doc->id,
                'name' => $doc->name,
                'file_name' => $doc->file_name,
                'size' => $doc->size,
                'mime_type' => $doc->mime_type,
                'disk_relative_path' => $doc->disk_relative_path,
                'thumbnail_url' => $doc->thumbnailUrl(),
                'download_url' => $doc->download_url,
            ];
        })->values()->all();
    }
}

if (! function_exists('sync_merchant_disk_documents')) {
    /**
     * Sync merchant business/bank documents on the public disk from document[] (max 5).
     * Moves tmp/uploads into shared/merchants/.../{userId}/ and deletes removed files.
     *
     * @param  callable(string): iterable<string>  $storageCandidatesForInput
     */
    function sync_merchant_disk_documents(int $userId, string $collection, array $rawDocumentInputs, callable $storageCandidatesForInput): void
    {
        if (! in_array($collection, ['business_documents', 'bank_documents'], true)) {
            return;
        }
        $prefix = $collection === 'business_documents'
            ? ExportStoragePaths::merchantBusinessDocumentUserPrefix($userId)
            : ExportStoragePaths::merchantBankDocumentUserPrefix($userId);

        $disk = Storage::disk('public');
        if (! $disk->exists($prefix)) {
            $disk->makeDirectory($prefix);
        }

        $kept = collect($rawDocumentInputs)
            ->map(function ($d) {
                return is_string($d) ? trim($d) : '';
            })
            ->filter(function ($d) {
                return $d !== '' && $d !== 'undefined';
            })
            ->values()
            ->take(5);

        $resolvedPaths = [];
        foreach ($kept as $input) {
            $resolved = null;
            foreach ($storageCandidatesForInput($input) as $candidate) {
                $candidate = ltrim(str_replace('\\', '/', (string) $candidate), '/');
                if ($candidate === '' || ! $disk->exists($candidate)) {
                    continue;
                }
                if (! merchant_merchant_document_sync_path_allowed($candidate, $userId, $collection)) {
                    continue;
                }
                $resolved = $candidate;
                break;
            }
            if ($resolved !== null) {
                $resolvedPaths[] = $resolved;
            }
        }
        $resolvedPaths = array_values(array_unique($resolvedPaths));

        $finalList = [];
        foreach ($resolvedPaths as $path) {
            if (strpos($path, 'tmp/uploads/') === 0) {
                $basename = basename($path);
                $dest = $prefix.'/'.$basename;
                $n = 0;
                while ($disk->exists($dest)) {
                    $n++;
                    $pi = pathinfo($basename);
                    $ext = isset($pi['extension']) && $pi['extension'] !== '' ? '.'.$pi['extension'] : '';
                    $stem = isset($pi['extension']) && $pi['extension'] !== '' ? substr($basename, 0, -strlen($ext)) : $basename;
                    $dest = $prefix.'/'.$stem.'-'.$n.$ext;
                }
                $disk->move($path, $dest);
                $finalList[] = $dest;
            } elseif (strpos($path, $prefix.'/') === 0) {
                $finalList[] = $path;
            }
        }
        $finalList = array_values(array_unique($finalList));

        foreach ($disk->files($prefix) as $existing) {
            $existing = ltrim(str_replace('\\', '/', $existing), '/');
            if (! in_array($existing, $finalList, true)) {
                $disk->delete($existing);
            }
=======
if (!function_exists('addFile')){
    function addFile($value, $path)
    {
        if (file_exists(Storage::disk('public')->path('downloads/'.$value))){
            return url('storage/downloads/'.$value);
        }
        else if(Storage::disk('oci')->exists($value)){
            $stream = Storage::disk('oci')->readStream($value);
            $localPath = 'downloads/' . $path . '/' . basename($value);
            Storage::disk('public')->put($localPath, $stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
            return url('storage/downloads/'.$value);
        }
        else{
            return '/images/no-image.jpg';
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4
        }
    }
}

<<<<<<< HEAD
if (! function_exists('merchant_replace_merchant_disk_documents_from_uploads')) {
    /**
     * Replace all files in a merchant document directory with the given uploads (API-style full replace).
     *
     * @param  iterable<int, \Illuminate\Http\UploadedFile|\Symfony\Component\HttpFoundation\File\UploadedFile>  $files
     */
    function merchant_replace_merchant_disk_documents_from_uploads(int $userId, string $collection, iterable $files): void
    {
        if (! in_array($collection, ['business_documents', 'bank_documents'], true)) {
            return;
        }
        $prefix = $collection === 'business_documents'
            ? ExportStoragePaths::merchantBusinessDocumentUserPrefix($userId)
            : ExportStoragePaths::merchantBankDocumentUserPrefix($userId);
        $disk = Storage::disk('public');
        if ($disk->exists($prefix)) {
            foreach ($disk->files($prefix) as $f) {
                $disk->delete($f);
            }
        }
        $disk->makeDirectory($prefix);
        foreach ($files as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }
            $name = merchant_document_unique_filename($file);
            $file->storeAs($prefix, $name, 'public');
=======
if (!function_exists('getFile')){
    function getFile($value)
    {
        if (file_exists(Storage::disk('public')->path('downloads/'.$value))){
            return url('storage/downloads/'.$value);
        }
        else if(Storage::disk('oci')->exists($value)){
            $stream = Storage::disk('oci')->readStream($value);
            $localPath = 'downloads/' . $value;
            Storage::disk('public')->put($localPath, $stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
            return url('storage/downloads/'.$value);
        }
        else{
            return '/images/no-image.jpg';
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4
        }
    }
}

<<<<<<< HEAD
if (! function_exists('store_merchant_logo')) {
    /**
     * Store a merchant business logo on the public disk under shared/merchants/logos/
     * (after optional oci_bucket_object_prefix()).
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @return string  e.g. "shared/merchants/logos/1234567890_42.png" or "staging/shared/merchants/logos/..."
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
     * - {prefix}/shared/merchants/logos/* (canonical; prefix from OCI_BUCKET_PREFIX)
     * - legacy shared/exports/merchants/logos/*, logos/*, uploads/*
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

if (! function_exists('merchant_bills_backgrounds_disk_path')) {
    /**
     * Root on the public disk for merchant bill backgrounds (admin-aligned): {prefix}shared/merchants/bills_backgrounds
     */
    function merchant_bills_backgrounds_disk_path(): string
    {
        return ExportStoragePaths::merchantBillsBackgroundsRoot();
    }
}

if (! function_exists('storage_read_public_disk_export_contents')) {
    /**
 * Read export file bytes (OCI primary when enabled, then public / public-local; legacy merchant-bills/).
 */
    function storage_read_public_disk_export_contents(string $relativePath): ?string
    {
        $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');
        $candidates = [$relativePath];
        if (strpos($relativePath, '/') === false) {
            $candidates[] = 'merchant-bills/'.$relativePath;
            $candidates[] = 'transfer-bills/'.$relativePath;
        }
        $prefix = oci_bucket_object_prefix();
        if ($prefix !== '' && strpos($relativePath, $prefix) !== 0) {
            $candidates[] = $prefix.$relativePath;
        }
        if ($prefix !== '' && strpos($relativePath, $prefix) === 0) {
            $stripped = substr($relativePath, strlen($prefix));
            if ($stripped !== '' && $stripped !== $relativePath) {
                $candidates[] = $stripped;
            }
        }
        $candidates = array_values(array_unique(array_filter($candidates)));

        $useOci = (bool) config('oci.enabled', false) && (bool) config('oci.public_disk_enabled', true);
        if ($useOci) {
            foreach ($candidates as $key) {
                if (Storage::disk('oci')->exists($key)) {
                    return Storage::disk('oci')->get($key);
                }
            }
        }

        foreach ($candidates as $key) {
            if (Storage::disk('public')->exists($key)) {
                return Storage::disk('public')->get($key);
            }
        }
        foreach ($candidates as $key) {
            if (Storage::disk('public-local')->exists($key)) {
                return Storage::disk('public-local')->get($key);
            }
        }
        $base = basename($relativePath);
        $legacyKey = 'merchant-bills/'.$base;
        if (Storage::disk('local')->exists($legacyKey)) {
            return Storage::disk('local')->get($legacyKey);
        }
        $legacyTransferKey = 'transfer-bills/'.$base;
        if (Storage::disk('local')->exists($legacyTransferKey)) {
            return Storage::disk('local')->get($legacyTransferKey);
        }

=======
if (!function_exists('downloadFile')){
    function downloadFile($filePath, $fileName)
    {
        if (file_exists(Storage::disk('public')->path('downloads/'.$filePath))){
            return Action::download(url('storage/downloads/' . $filePath), $fileName);
        } else if(Storage::disk('oci')->exists($filePath)){
            $stream = Storage::disk('oci')->readStream($filePath);
            $localPath = 'downloads/' . $filePath;
            Storage::disk('public')->put($localPath, $stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
            return Action::download(url('storage/downloads/' . $filePath), $fileName);
        } else
            return Action::danger(404);
    }
}

if (!function_exists('getFilePath')) {
    function getFilePath($name, $id)
    {
        $relativePath = "downloads/reports/{$name}/{$name}_{$id}.xlsx";
        $localFullPath = storage_path("app/public/{$relativePath}");
        $ociPath = "reports/{$name}/{$name}_{$id}.xlsx";
        if (file_exists($localFullPath)) {
            return url("storage/{$relativePath}");
        }
        if (Storage::disk('oci')->exists($ociPath)) {
            $stream = Storage::disk('oci')->readStream($ociPath);
            Storage::disk('public')->makeDirectory("downloads/reports/{$name}");
            Storage::disk('public')->put($relativePath, $stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
            return url("storage/{$relativePath}");
        }
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4
        return null;
    }
}

<<<<<<< HEAD
if (! function_exists('bills_background_disk_path')) {
    /**
     * @deprecated Legacy flat directory; new uploads use merchant_bills_backgrounds_disk_path() + user id.
     */
    function bills_background_disk_path(): string
    {
        return 'bills_backgrounds';
    }
}

if (! function_exists('ensure_bills_background_directory')) {
    /**
     * Ensure shared/merchants/bills_backgrounds root exists on the public disk (OCI/local).
     */
    function ensure_bills_background_directory(): void
    {
        $disk = Storage::disk('public');
        $dir = merchant_bills_backgrounds_disk_path();

        if (! $disk->exists($dir)) {
            $disk->makeDirectory($dir);
        }
    }
}

if (! function_exists('store_bill_background_image')) {
    /**
     * Store bill UI background image on the public disk under shared/merchants/bills_backgrounds/{userId}/ (OCI when enabled).
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  int  $userId  Merchant owner user id (store main user)
     * @return string  e.g. "{prefix}shared/merchants/bills_backgrounds/42/1779210942_42.png"
     */
    function store_bill_background_image($file, int $userId): string
    {
        $disk = Storage::disk('public');
        $dir = ExportStoragePaths::merchantBillsBackgroundUserPrefix($userId);

        if (! $disk->exists($dir)) {
            $disk->makeDirectory($dir);
        }

        $extension = $file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'png';
        $filename = time().'_'.$userId.'.'.strtolower($extension);

        return $disk->putFileAs($dir, $file, $filename);
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

        // Also remove if file was moved to shared/merchants/bills_backgrounds/{userId}/ (same basename pattern)
        if (strpos($path, bills_background_disk_path().'/') === 0) {
            $base = basename($path);
            if (preg_match('/^(\d+)_(\d+)\./', $base, $m)) {
                $candidate = ExportStoragePaths::merchantBillsBackgroundUserPrefix((int) $m[2]).'/'.$base;
                if ($candidate !== $path && Storage::disk('public')->exists($candidate)) {
                    Storage::disk('public')->delete($candidate);
                }
            }
        }

        if (strpos($path, 'uploads/') === 0 && is_file(public_path($path))) {
            @unlink(public_path($path));
=======

if (!function_exists('getSettings')){
    function getSettings()
    {
        if (file_exists(Storage::disk('public')->path('downloads/app/settings.json'))){
            return storage_path('app/downloads/app/settings.json');
        }
        else if(Storage::disk('oci')->exists('app/settings.json')){
            $stream = Storage::disk('oci')->readStream('app/settings.json');
            $localPath = 'downloads/app/settings.json';
            Storage::disk('public')->put($localPath, $stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
            return storage_path('app/downloads/app/settings.json');
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4
        }
    }
}

<<<<<<< HEAD
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

        // Legacy flat bills_backgrounds/{time}_{userId}.ext → shared/merchants/bills_backgrounds/{userId}/…
        if (strpos($path, bills_background_disk_path().'/') === 0) {
            $base = basename($path);
            if (preg_match('/^(\d+)_(\d+)\./', $base, $m)) {
                $candidate = ExportStoragePaths::merchantBillsBackgroundUserPrefix((int) $m[2]).'/'.$base;
                if (Storage::disk('public')->exists($candidate)) {
                    return media_route_url($candidate);
                }
            }
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
=======
if (!function_exists('uploadFile')){
    function uploadFile($file)
    {
        $filePath = storage_path($file);
        if (file_exists($filePath)) {
            Storage::disk('oci')->put($filePath, fopen($filePath, 'r+'));
        }
    }
}

>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4
