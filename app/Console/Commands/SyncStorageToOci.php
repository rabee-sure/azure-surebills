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
        $ociDisk = Storage::disk('oci');

        $paths = [
            storage_path('app/public') => '',
            storage_path('logs')       => 'logs/',
        ];

        $allFiles = [];

        foreach ($paths as $basePath => $prefix) {

            if (!is_dir($basePath)) {
                continue;
            }

            $files = \Illuminate\Support\Facades\File::allFiles($basePath);

            foreach ($files as $file) {

                $relativePath = $prefix . str_replace(
                    $basePath . DIRECTORY_SEPARATOR,
                    '',
                    $file->getPathname()
                );

                $allFiles[] = [
                    'full_path'     => $file->getPathname(),
                    'relative_path' => str_replace('\\', '/', $relativePath),
                ];
            }
        }

        if (empty($allFiles)) {
            $this->warn('No files found to upload.');
            return;
        }

        $bar = $this->output->createProgressBar(count($allFiles));
        $bar->start();

        foreach ($allFiles as $file) {

            try {
                $stream = fopen($file['full_path'], 'r');

                $ociDisk->put($file['relative_path'], $stream);

                if (is_resource($stream)) {
                    fclose($stream);
                }

            } catch (\Throwable $e) {
                $this->error("\nFailed to upload {$file['relative_path']}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();

        $this->info("\nSync complete! Uploaded " . count($allFiles) . " file(s) to OCI.");
    }



}
