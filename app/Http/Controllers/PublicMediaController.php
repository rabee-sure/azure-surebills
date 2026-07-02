<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Serves files from the public storage disk when public/storage symlink is not used.
 */
class PublicMediaController extends Controller
{
    /**
     * Allowed path prefixes (no directory traversal).
     * Canonical merchant logos use merchant_logo_disk_path() (shared/merchants/logos + optional OCI_BUCKET_PREFIX).
     */
    protected const ALLOWED_PREFIXES = [
        'shared/merchants/logos/',
        'shared/merchants/business_documents/',
        'shared/merchants/bank_documents/',
        'shared/merchants/bills_backgrounds/',
        'shared/bills/',
        'shared/exports/merchants/logos/',
        'shared/exports/merchants/bills/',
        'logos/',
        'bills_backgrounds/',
        'products/',
        'categories/',
        'reports/',
        'shared/bills/',
        'shared/transfers/',
        'transfers/',
        'automatic_transfers/',
        'summary_transfers/',
        'downloads/',
    ];

    public function show(Request $request, string $path): StreamedResponse
    {
        $path = $this->sanitizePath($path);

        if ($path === '' || ! $this->isAllowedPath($path)) {
            abort(404);
        }

        $disk = $this->resolveDisk($path);

        if ($disk === null) {
            abort(404);
        }

        $mime = Storage::disk($disk)->mimeType($path) ?: 'application/octet-stream';

        return Storage::disk($disk)->response($path, null, [
            'Content-Type' => $mime,
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }

    protected function sanitizePath(string $path): string
    {
        $path = str_replace(['\\', "\0"], '', $path);
        $path = ltrim($path, '/');

        if (strpos($path, '..') !== false) {
            return '';
        }

        return $path;
    }

    protected function isAllowedPath(string $path): bool
    {
        $logoDir = merchant_logo_disk_path();
        if ($logoDir !== '' && strlen($path) > strlen($logoDir) && strpos($path, $logoDir.'/') === 0) {
            return true;
        }

        $businessDocs = merchant_business_documents_disk_path();
        if ($businessDocs !== '' && strlen($path) > strlen($businessDocs) && strpos($path, $businessDocs.'/') === 0) {
            return true;
        }

        $bankDocs = merchant_bank_documents_disk_path();
        if ($bankDocs !== '' && strlen($path) > strlen($bankDocs) && strpos($path, $bankDocs.'/') === 0) {
            return true;
        }

        $billBgs = merchant_bills_backgrounds_disk_path();
        if ($billBgs !== '' && strlen($path) > strlen($billBgs) && strpos($path, $billBgs.'/') === 0) {
            return true;
        }

        $exportsBills = \App\Support\Storage\ExportStoragePaths::merchantBillsExportsRoot();
        if ($exportsBills !== '' && strlen($path) > strlen($exportsBills) && strpos($path, $exportsBills.'/') === 0) {
            return true;
        }

        foreach (self::ALLOWED_PREFIXES as $prefix) {
            if (strpos($path, $prefix) === 0) {
                return true;
            }
        }

        // Root-level Nova hashes (legacy uploads on public disk)
        return (bool) preg_match('#^[a-zA-Z0-9._-]+\.(png|jpe?g|gif|webp|pdf|xlsx?|csv|docx?)$#i', $path);
    }

    protected function resolveDisk(string $path): ?string
    {
        if (Storage::disk('public')->exists($path)) {
            return 'public';
        }

        if (Storage::disk('public-local')->exists($path)) {
            return 'public-local';
        }

        return null;
    }
}
