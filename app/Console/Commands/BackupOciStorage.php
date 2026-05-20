<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class BackupOciStorage extends Command
{
    protected $signature = 'storage:backup-oci
                            {--disk=oci : Disk to read from (oci or oci-private)}
                            {--path= : Only include objects under this prefix}
                            {--output= : Zip path relative to storage/app, or an absolute path}';

    protected $description = 'Download objects from the configured OCI bucket into a local .zip backup';

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

        $this->info("Reading from bucket: {$bucket} (disk: {$diskName})");
        if ($directory) {
            $this->info("Prefix: {$directory}/");
        }

        $this->line('Listing objects...');
        $files = $disk->allFiles($directory);
        $count = count($files);

        if ($count === 0) {
            $this->warn('No objects found. Nothing to back up.');

            return self::FAILURE;
        }

        $zipPath = $this->resolveZipPath();

        $zipDir = dirname($zipPath);
        if (! is_dir($zipDir) && ! @mkdir($zipDir, 0755, true) && ! is_dir($zipDir)) {
            $this->error("Could not create directory: {$zipDir}");

            return self::FAILURE;
        }

        if (is_file($zipPath)) {
            @unlink($zipPath);
        }

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $this->error("Could not open zip for writing: {$zipPath}");

            return self::FAILURE;
        }

        $added = 0;
        $failed = 0;
        $tempFiles = [];
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($files as $file) {
            $bar->advance();

            try {
                $stream = $disk->readStream($file);
                if ($stream === false) {
                    $failed++;
                    $this->newLine();
                    $this->error("  Failed to read stream: {$file}");

                    continue;
                }

                $tempPath = tempnam(sys_get_temp_dir(), 'oci_bak_');
                $out = fopen($tempPath, 'wb');
                if ($out === false) {
                    fclose($stream);
                    $failed++;
                    $this->newLine();
                    $this->error("  Failed temp file for: {$file}");

                    continue;
                }

                stream_copy_to_stream($stream, $out);
                fclose($out);
                fclose($stream);

                $nameInZip = str_replace('\\', '/', $file);
                if (! $zip->addFile($tempPath, $nameInZip)) {
                    @unlink($tempPath);
                    $failed++;
                    $this->newLine();
                    $this->error("  Failed to add to zip: {$file}");

                    continue;
                }

                $tempFiles[] = $tempPath;
                $added++;
            } catch (\Throwable $e) {
                $failed++;
                $this->newLine();
                $this->error("  Failed: {$file} — {$e->getMessage()}");
            }
        }

        $bar->finish();
        $this->newLine();

        if (! $zip->close()) {
            foreach ($tempFiles as $t) {
                @unlink($t);
            }
            $this->error('Could not finalize zip archive.');

            return self::FAILURE;
        }

        foreach ($tempFiles as $t) {
            @unlink($t);
        }

        $this->newLine();
        $this->table(
            ['Metric', 'Count'],
            [
                ['Objects in bucket', $count],
                ['Added to zip', $added],
                ['Failed', $failed],
            ]
        );

        $this->info('Backup written to: '.$zipPath);

        if ($failed > 0) {
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function resolveZipPath(): string
    {
        $output = $this->option('output');
        if (is_string($output) && $output !== '') {
            $trimmed = trim($output);
            if (preg_match('#^([a-zA-Z]:[/\\\\]|/)#', $trimmed)) {
                return $trimmed;
            }

            return storage_path('app/'.ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $trimmed), DIRECTORY_SEPARATOR));
        }

        $defaultDir = storage_path('app'.DIRECTORY_SEPARATOR.'backups');
        $name = 'oci-storage-'.now()->format('Y-m-d_His').'.zip';

        return $defaultDir.DIRECTORY_SEPARATOR.$name;
    }
}
