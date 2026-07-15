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
        $dir = $this->option('dir') ?: '';
        $localDisk = Storage::disk('local');
        $ociDisk = Storage::disk('oci');
        $allFiles = $localDisk->allFiles($dir);
        $ociDisk->put('app/settings.json', file_get_contents(storage_path('app/settings.json')));

        if (empty($allFiles)) {
            $this->warn('No files found to upload.');
            return;
        }

        $bar = $this->output->createProgressBar(count($allFiles));
        $bar->start();

        foreach ($allFiles as $path) {
            try {
                $content = $localDisk->get($path);
                $ociDisk->put($path, $content);
            } catch (\Throwable $e) {
                $this->error("\n❌ Failed to upload {$path}: " . $e->getMessage());
            }
            $bar->advance();
        }

        Media::query()->update(['disk' => 'oci', 'conversions_disk' => 'oci']);
        $bar->finish();
        $this->info("\n✅ Sync complete! Uploaded " . count($allFiles) . " file(s) to OCI.");
    }
}
