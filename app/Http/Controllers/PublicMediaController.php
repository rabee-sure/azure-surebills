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
     */
    protected const ALLOWED_PREFIXES = [
        'logos/',
        'bills_backgrounds/',
        'products/',
        'categories/',
        'reports/',
        'transfers/',
        'automatic_transfers/',
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
