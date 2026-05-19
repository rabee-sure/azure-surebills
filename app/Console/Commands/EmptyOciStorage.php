<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class EmptyOciStorage extends Command
{
    protected $signature = 'storage:empty-oci
                            {--dry-run : List objects without deleting}
                            {--path= : Only delete objects under this prefix}
                            {--disk=oci : Disk to empty (oci or oci-private)}
                            {--force : Skip confirmation prompt}';

    protected $description = 'Delete all objects from the OCI bucket so you can re-run storage:migrate-to-oci';

    public function handle(): int
    {
        $diskName = $this->option('disk');

        if (! in_array($diskName, ['oci', 'oci-private'], true)) {
            $this->error('Invalid --disk. Use "oci" or "oci-private".');

            return self::FAILURE;
        }

        if (empty(config('oci.access_key')) || empty(config('oci.bucket'))) {
            $this->error('OCI credentials are not configured. Set OCI_ACCESS_KEY, OCI_SECRET_KEY, OCI_BUCKET, and OCI_ENDPOINT in .env.');

            return self::FAILURE;
        }

        $disk = Storage::disk($diskName);
        $bucket = config('filesystems.disks.'.$diskName.'.bucket');
        $prefix = $this->option('path') ? trim($this->option('path'), '/').'/' : '';
        $directory = $prefix !== '' ? rtrim($prefix, '/') : null;
        $dryRun = (bool) $this->option('dry-run');

        $this->info("Bucket: {$bucket} (disk: {$diskName})");
        if ($directory) {
            $this->info("Prefix: {$directory}/");
        }

        $this->line('Listing objects...');
        $files = $disk->allFiles($directory);
        $count = count($files);

        if ($count === 0) {
            $this->info('Bucket is already empty (no objects found).');

            return self::SUCCESS;
        }

        $this->warn("Found {$count} object(s) to delete.");

        if ($dryRun) {
            foreach (array_slice($files, 0, 20) as $file) {
                $this->line("  [dry-run] would delete: {$file}");
            }
            if ($count > 20) {
                $this->line('  ... and '.($count - 20).' more');
            }
            $this->info('Dry run complete. No objects were deleted.');

            return self::SUCCESS;
        }

        if (! $this->option('force') && ! $this->confirm("Delete all {$count} object(s) from bucket \"{$bucket}\"?")) {
            $this->info('Aborted.');

            return self::SUCCESS;
        }

        $deleted = 0;
        $failed = 0;
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($files as $file) {
            $bar->advance();

            try {
                if ($disk->delete($file)) {
                    $deleted++;
                } else {
                    $failed++;
                }
            } catch (\Throwable $e) {
                $failed++;
                $this->newLine();
                $this->error("  Failed: {$file} — {$e->getMessage()}");
            }
        }

        $bar->finish();
        $this->newLine(2);
        $this->table(
            ['Metric', 'Count'],
            [
                ['Deleted', $deleted],
                ['Failed', $failed],
            ]
        );

        if ($failed > 0) {
            return self::FAILURE;
        }

        $this->info('Bucket emptied. Re-sync with: php artisan storage:migrate-to-oci');

        return self::SUCCESS;
    }
}
