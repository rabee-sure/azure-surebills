<?php

namespace App\Filesystem;

use Illuminate\Contracts\Filesystem\Cloud as CloudContract;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Carbon;

/**
 * Reads from primary (OCI) first, then local fallback. Writes always go to primary.
 *
 * Enables zero-downtime migration: existing local files remain readable while new
 * uploads are stored in OCI Object Storage.
 *
 * Flysystem note (PR-06): do not type-hint League\Flysystem\FilesystemInterface.
 * That interface exists only on Flysystem 1.x (Laravel 8). Laravel 12 uses Flysystem 3
 * (FilesystemOperator). Returning the primary driver's getDriver() without a League
 * return type keeps this adapter compatible with both stacks after the PR-08 package cutover.
 */
class FallbackFilesystemAdapter implements CloudContract
{
    /** @var FilesystemAdapter */
    protected $primary;

    /** @var FilesystemAdapter */
    protected $fallback;

    public function __construct(FilesystemAdapter $primary, FilesystemAdapter $fallback)
    {
        $this->primary = $primary;
        $this->fallback = $fallback;
    }

    /**
     * Underlying Flysystem instance (writes use the primary disk, e.g. OCI).
     *
     * Must not be routed through {@see __call}: calling
     * {@see FilesystemAdapter::__call}('getDriver') would forward to the inner
     * Flysystem driver, which has no getDriver() — breaks Spatie Media Library
     * and other packages that expect Laravel's adapter API.
     *
     * @return mixed Flysystem 1 FilesystemInterface or Flysystem 3 FilesystemOperator
     */
    public function getDriver()
    {
        return $this->primary->getDriver();
    }

    /**
     * Resolve which disk holds the file for read operations.
     */
    protected function diskForRead(string $path): ?FilesystemAdapter
    {
        if ($this->diskExists($this->primary, $path)) {
            return $this->primary;
        }

        if ($this->diskExists($this->fallback, $path)) {
            return $this->fallback;
        }

        return null;
    }

    /**
     * Safe exists check for Flysystem 3: network/API failures on primary must
     * not abort fallback reads (UnableToCheckFileExistence replaces soft false).
     */
    protected function diskExists(FilesystemAdapter $disk, string $path): bool
    {
        try {
            return $disk->exists($path);
        } catch (\League\Flysystem\UnableToCheckExistence|\League\Flysystem\UnableToCheckFileExistence $e) {
            return false;
        } catch (\Throwable $e) {
            // Preserve prior soft-fail behavior for transient OCI/S3 connectivity issues.
            if ($disk === $this->primary) {
                return false;
            }

            throw $e;
        }
    }

    public function exists($path): bool
    {
        return $this->diskExists($this->primary, $path) || $this->diskExists($this->fallback, $path);
    }

    /**
     * Flysystem / legacy alias for exists() (used in Blade: Storage::disk('public')->has()).
     */
    public function has($path): bool
    {
        return $this->exists($path);
    }

    public function missing($path): bool
    {
        return ! $this->exists($path);
    }

    public function get($path)
    {
        $disk = $this->diskForRead($path);

        if ($disk === null) {
            throw new FileNotFoundException("File not found at path: {$path}");
        }

        return $disk->get($path);
    }

    public function readStream($path)
    {
        $disk = $this->diskForRead($path);

        if ($disk === null) {
            throw new FileNotFoundException("File not found at path: {$path}");
        }

        return $disk->readStream($path);
    }

    public function put($path, $contents, $options = []): bool
    {
        return $this->primary->put($path, $contents, $options);
    }

    public function writeStream($path, $resource, array $options = []): bool
    {
        return $this->primary->writeStream($path, $resource, $options);
    }

    public function getVisibility($path): string
    {
        $disk = $this->diskForRead($path);

        return $disk ? $disk->getVisibility($path) : 'private';
    }

    public function setVisibility($path, $visibility): bool
    {
        if ($this->primary->exists($path)) {
            return $this->primary->setVisibility($path, $visibility);
        }

        if ($this->fallback->exists($path)) {
            return $this->fallback->setVisibility($path, $visibility);
        }

        return $this->primary->setVisibility($path, $visibility);
    }

    public function prepend($path, $data): bool
    {
        return $this->primary->prepend($path, $data);
    }

    public function append($path, $data): bool
    {
        return $this->primary->append($path, $data);
    }

    public function delete($paths): bool
    {
        $paths = is_array($paths) ? $paths : func_get_args();
        $success = true;

        foreach ($paths as $path) {
            if ($this->diskExists($this->primary, $path)) {
                $success = $this->primary->delete($path) && $success;
            }
            if ($this->diskExists($this->fallback, $path)) {
                $success = $this->fallback->delete($path) && $success;
            }
        }

        return $success;
    }

    public function copy($from, $to): bool
    {
        $contents = $this->get($from);

        return $this->put($to, $contents);
    }

    public function move($from, $to): bool
    {
        if ($this->copy($from, $to)) {
            return $this->delete($from);
        }

        return false;
    }

    public function size($path): int
    {
        $disk = $this->diskForRead($path);

        if ($disk === null) {
            throw new FileNotFoundException("File not found at path: {$path}");
        }

        return $disk->size($path);
    }

    public function lastModified($path): int
    {
        $disk = $this->diskForRead($path);

        if ($disk === null) {
            throw new FileNotFoundException("File not found at path: {$path}");
        }

        return $disk->lastModified($path);
    }

    public function files($directory = null, $recursive = false): array
    {
        return array_values(array_unique(array_merge(
            $this->primary->files($directory, $recursive),
            $this->fallback->files($directory, $recursive)
        )));
    }

    public function allFiles($directory = null): array
    {
        return array_values(array_unique(array_merge(
            $this->primary->allFiles($directory),
            $this->fallback->allFiles($directory)
        )));
    }

    public function directories($directory = null, $recursive = false): array
    {
        return array_values(array_unique(array_merge(
            $this->primary->directories($directory, $recursive),
            $this->fallback->directories($directory, $recursive)
        )));
    }

    public function allDirectories($directory = null): array
    {
        return array_values(array_unique(array_merge(
            $this->primary->allDirectories($directory),
            $this->fallback->allDirectories($directory)
        )));
    }

    public function makeDirectory($path): bool
    {
        return $this->primary->makeDirectory($path);
    }

    public function deleteDirectory($directory): bool
    {
        $primary = $this->primary->deleteDirectory($directory);
        $fallback = $this->fallback->deleteDirectory($directory);

        return $primary || $fallback;
    }

    public function path($path): string
    {
        $disk = $this->diskForRead($path);

        if ($disk === null) {
            return $this->primary->path($path);
        }

        return $disk->path($path);
    }

    /**
     * @param  string|\Illuminate\Http\File|\Illuminate\Http\UploadedFile|null  $file
     */
    public function putFile($path, $file = null, $options = [])
    {
        return $this->primary->putFile($path, $file, $options);
    }

    /**
     * @param  string|\Illuminate\Http\File|\Illuminate\Http\UploadedFile  $file
     * @param  string|null  $name
     */
    public function putFileAs($path, $file, $name = null, $options = [])
    {
        return $this->primary->putFileAs($path, $file, $name, $options);
    }

    public function url($path): string
    {
        if ($this->primary->exists($path)) {
            return $this->urlForDisk($this->primary, $path);
        }

        if ($this->fallback->exists($path)) {
            return $this->urlForDisk($this->fallback, $path);
        }

        return $this->urlForDisk($this->primary, $path);
    }

    protected function urlForDisk(FilesystemAdapter $disk, string $path): string
    {
        if ($this->shouldUseSignedUrl($disk)) {
            return $disk->temporaryUrl(
                $path,
                Carbon::now()->addMinutes((int) config('oci.signed_url_expiration', 30))
            );
        }

        return $disk->url($path);
    }

    protected function shouldUseSignedUrl(FilesystemAdapter $disk): bool
    {
        if ($disk !== $this->primary) {
            return false;
        }

        return config('oci.visibility', 'private') === 'private';
    }

    public function temporaryUrl($path, $expiration, array $options = []): string
    {
        $disk = $this->diskForRead($path);

        if ($disk === null) {
            throw new FileNotFoundException("File not found at path: {$path}");
        }

        if ($disk === $this->primary && method_exists($disk, 'temporaryUrl')) {
            return $disk->temporaryUrl($path, $expiration, $options);
        }

        return $disk->url($path);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download($path, $name = null, array $headers = [])
    {
        $disk = $this->diskForRead($path);

        if ($disk === null) {
            throw new FileNotFoundException("File not found at path: {$path}");
        }

        return $disk->download($path, $name, $headers);
    }

    public function response($path, $name = null, array $headers = [], $disposition = 'inline')
    {
        $disk = $this->diskForRead($path);

        if ($disk === null) {
            throw new FileNotFoundException("File not found at path: {$path}");
        }

        return $disk->response($path, $name, $headers, $disposition);
    }

    public function mimeType($path)
    {
        $disk = $this->diskForRead($path);

        if ($disk === null) {
            return false;
        }

        return $disk->mimeType($path);
    }

    public function providesTemporaryUrls(): bool
    {
        return method_exists($this->primary, 'providesTemporaryUrls') && $this->primary->providesTemporaryUrls();
    }

    /**
     * Forward unknown methods to the underlying adapter (Flysystem compatibility).
     */
    public function __call($method, array $parameters)
    {
        if ($method === 'has' && isset($parameters[0])) {
            return $this->exists($parameters[0]);
        }

        return $this->primary->__call($method, $parameters);
    }
}
