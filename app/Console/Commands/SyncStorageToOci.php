<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SyncStorageToOci extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Example: php artisan oci:sync
     */
    protected $signature = 'oci:sync {--dir= : Specific subdirectory under storage/app to sync (optional)}';

    /**
     * The console command description.
     */
    protected $description = 'Upload all files from storage/app (or subdirectory) to the OCI bucket';

    public function handle()
    {
        $ociDisk   = Storage::disk('oci');
        $localDisk = Storage::disk('local');

        $paths = [
            storage_path('app'),
            storage_path('logs'),
        ];

        $allFiles = [];

        foreach ($paths as $basePath) {

            $files = \Illuminate\Support\Facades\File::allFiles($basePath);

            foreach ($files as $file) {

                $relativePath = str_replace(storage_path() . DIRECTORY_SEPARATOR, '', $file->getPathname());

                $allFiles[] = $relativePath;
            }
        }

        if (empty($allFiles)) {
            $this->warn('No files found to upload.');
            return;
        }

        $bar = $this->output->createProgressBar(count($allFiles));
        $bar->start();

        foreach ($allFiles as $relativePath) {

            try {

                $fullPath = storage_path($relativePath);

                $stream = fopen($fullPath, 'r');

                $ociDisk->put($relativePath, $stream);

                if (is_resource($stream)) {
                    fclose($stream);
                }

            } catch (\Throwable $e) {
                $this->error("\nFailed to upload {$relativePath}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();

        $this->info("\n Sync complete! Uploaded " . count($allFiles) . " file(s) to OCI.");
    }

}
