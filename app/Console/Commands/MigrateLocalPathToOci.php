<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

/**
 * Flexible local → OCI (or disk → disk) path migration.
 *
 * Disks:
 *   public-local  → storage/app/public (default source)
 *   web-public    → project public/ folder (uploads, etc.) — registered by this command
 *   oci           → OCI bucket (default destination)
 *
 * Examples:
 *   php artisan storage:migrate-path logos shared/merchants/logos --dry-run
 *   php artisan storage:migrate-path transfers shared/transfers
 *   php artisan storage:migrate-path uploads/bills_backgrounds shared/merchants/bills_backgrounds --from-disk=web-public
 *   php artisan storage:migrate-path uploads shared/merchants/logos --from-disk=web-public
 */
class MigrateLocalPathToOci extends Command
{
    protected $signature = 'storage:migrate-path
                            {source : Source path relative to the source disk (file or directory)}
                            {destination : Destination path relative to the destination disk (file or directory)}
                            {--from-disk=public-local : Source disk (public-local|web-public|local|…)}
                            {--to-disk=oci : Destination disk (oci|…)}
                            {--dry-run : List planned copies without writing}
                            {--overwrite : Re-upload even when the destination key already exists}
                            {--delete-source : Delete each source file after a successful copy}';

    protected $description = 'Copy files from a local (or other) disk path to OCI/another disk, remapping source → destination prefixes';

    public function handle(): int
    {
        $fromDiskName = (string) $this->option('from-disk');
        $toDiskName = (string) $this->option('to-disk');

        $this->registerBuiltinDisks();

        if (! $this->diskExists($fromDiskName)) {
            $this->error("Unknown source disk [{$fromDiskName}]. Use public-local, web-public, local, or a configured disk.");

            return self::FAILURE;
        }

        if (! $this->diskExists($toDiskName)) {
            $this->error("Unknown destination disk [{$toDiskName}].");

            return self::FAILURE;
        }

        if ($toDiskName === 'oci' && function_exists('oci_storage_enabled') && ! oci_storage_enabled()) {
            $this->warn('OCI_ENABLED is false. Continuing with disk [oci] only if credentials/disks are still registered.');
        }

        $rawSource = (string) $this->argument('source');
        $rawDestination = (string) $this->argument('destination');

        if ($this->containsTraversal($rawSource) || $this->containsTraversal($rawDestination)) {
            $this->error('Source/destination must not contain ".." path segments.');

            return self::FAILURE;
        }

        $source = $this->normalizePath($rawSource);
        $destination = $this->normalizePath($rawDestination);

        // web-public root is already public_path(); allow "public/uploads/…" as a convenience.
        if ($fromDiskName === 'web-public' && strpos($source, 'public/') === 0) {
            $source = substr($source, strlen('public/'));
        }

        if ($source === '') {
            $this->error('Source path cannot be empty. Use a directory or file under the source disk.');

            return self::FAILURE;
        }

        $from = Storage::disk($fromDiskName);
        $to = Storage::disk($toDiskName);
        $dryRun = (bool) $this->option('dry-run');
        $overwrite = (bool) $this->option('overwrite');
        $deleteSource = (bool) $this->option('delete-source');

        $jobs = $this->buildJobs($from, $source, $destination);

        if ($jobs === null) {
            return self::FAILURE;
        }

        if (count($jobs) === 0) {
            $this->warn("No files found under [{$fromDiskName}:{$source}].");

            return self::SUCCESS;
        }

        $this->info(sprintf(
            'Migrating %d file(s): %s:%s → %s:%s%s',
            count($jobs),
            $fromDiskName,
            $source,
            $toDiskName,
            $destination !== '' ? $destination : '(bucket root)',
            $dryRun ? ' [dry-run]' : ''
        ));

        $uploaded = 0;
        $skipped = 0;
        $failed = 0;

        $bar = $this->output->createProgressBar(count($jobs));
        $bar->start();

        foreach ($jobs as $job) {
            $bar->advance();
            $src = $job['source'];
            $dst = $job['destination'];

            try {
                if (! $overwrite && $to->exists($dst)) {
                    $skipped++;
                    continue;
                }

                if ($dryRun) {
                    $this->line('');
                    $this->line("  [dry-run] {$src} → {$dst}");
                    $uploaded++;
                    continue;
                }

                $stream = $from->readStream($src);
                if ($stream === false || $stream === null) {
                    throw new \RuntimeException('Unable to open source stream');
                }

                $written = $to->writeStream($dst, $stream);
                if (is_resource($stream)) {
                    fclose($stream);
                }

                if ($written === false) {
                    throw new \RuntimeException('writeStream returned false');
                }

                $uploaded++;

                if ($deleteSource) {
                    $from->delete($src);
                }
            } catch (\Throwable $e) {
                $failed++;
                $this->line('');
                $this->error("  Failed: {$src} → {$dst} — {$e->getMessage()}");
            }
        }

        $bar->finish();
        $this->newLine(2);
        $this->table(
            ['Metric', 'Count'],
            [
                [$dryRun ? 'Would copy' : 'Copied', $uploaded],
                ['Skipped (already exists)', $skipped],
                ['Failed', $failed],
            ]
        );

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * Build the jobs for the migration
     * @param Filesystem $from The source disk
     * @param string $source The source path
     * @param string $destination The destination path
     * @return list<array{source: string, destination: string}>|null
     */
    private function buildJobs(Filesystem $from, string $source, string $destination): ?array
    {
        if ($from->exists($source) && $this->isLikelyFile($from, $source)) {
            $destKey = $this->destinationForSingleFile($source, $destination);

            return [[
                'source' => $source,
                'destination' => $destKey,
            ]];
        }

        // Directory (or prefix): list all files under source
        $files = $from->allFiles($source);
        $jobs = [];

        foreach ($files as $file) {
            $relative = $this->relativeUnderSource($file, $source);
            if ($relative === null) {
                continue;
            }

            $destKey = $destination === ''
                ? $relative
                : trim($destination.'/'.$relative, '/');

            $jobs[] = [
                'source' => $file,
                'destination' => $destKey,
            ];
        }

        return $jobs;
    }

    // Get the destination for a single file
    // If the destination is a directory, use the source file name as the destination
    // If the destination is a file, use the destination file name as the destination
    private function destinationForSingleFile(string $source, string $destination): string
    {
        if ($destination === '') {
            return basename($source);
        }

        // Destination looks like a directory when it has no extension or ends conceptually as a folder key.
        // If destination already includes a filename (has extension), use it as the exact key.
        $destBase = basename($destination);
        $hasExtension = strpos($destBase, '.') !== false;

        if ($hasExtension) {
            return $destination;
        }

        return trim($destination.'/'.basename($source), '/');
    }

    // Check if the path is likely a file
    private function isLikelyFile(Filesystem $disk, string $path): bool
    {
        // Flysystem v1/v3: directories may "exist"; prefer treating paths with no children listing ambiguity
        // as files when getSize/read works. allFiles on a file often returns [] or the file itself depending on driver.
        try {
            if (method_exists($disk, 'getMetadata')) {
                $meta = $disk->getMetadata($path);
                if (is_array($meta) && isset($meta['type'])) {
                    return $meta['type'] === 'file';
                }
            }
        } catch (\Throwable $e) {
            // fall through
        }

        try {
            return $disk->size($path) !== false;
        } catch (\Throwable $e) {
            return false;
        }
    }

    // Get the relative path under the source
    private function relativeUnderSource(string $file, string $source): ?string
    {
        $file = $this->normalizePath($file);
        $source = $this->normalizePath($source);

        if ($file === $source) {
            return basename($file);
        }

        $prefix = $source.'/';
        if (strpos($file, $prefix) !== 0) {
            return null;
        }

        return substr($file, strlen($prefix));
    }

    // Normalize the path to remove any trailing slashes and convert backslashes to forward slashes
    private function normalizePath(string $path): string
    {
        $path = str_replace('\\', '/', trim($path));
        $path = trim($path, '/');

        if ($path === '.' || $path === '*') {
            return '';
        }

        $parts = [];
        foreach (explode('/', $path) as $part) {
            if ($part === '' || $part === '.') {
                continue;
            }
            $parts[] = $part;
        }

        return implode('/', $parts);
    }

    // Check if the path contains any ".." path segments
    private function containsTraversal(string $path): bool
    {
        $path = str_replace('\\', '/', $path);

        foreach (explode('/', $path) as $part) {
            if ($part === '..') {
                return true;
            }
        }

        return false;
    }

    /**
     * Migration-only disks that are not always present in config/filesystems.php.
     */
    private function registerBuiltinDisks(): void
    {
        if (! $this->diskExists('web-public')) {
            config([
                'filesystems.disks.web-public' => [
                    'driver' => 'local',
                    'root' => public_path(),
                    'throw' => false,
                ],
            ]);
        }
    }

    private function diskExists(string $name): bool
    {
        return array_key_exists($name, config('filesystems.disks', []));
    }
}
