<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Finder\Finder;

class SyncStorageToOci extends Command
{
  protected $signature = 'oci:sync';

  protected $description = 'Sync all files from storage/app to OCI without app/public prefix';

  public function handle()
  {
    ini_set('memory_limit', '-1');
    set_time_limit(0);

    $ociDisk = Storage::disk('oci');

    $basePath = storage_path('app');

    $finder = new Finder();
    $finder->files()->in($basePath);

    $files = [];

    foreach ($finder as $file) {

      $fullPath = $file->getRealPath();

      // ✨ شيل storage/app/
      $relativePath = str_replace(
        $basePath . DIRECTORY_SEPARATOR,
        '',
        $fullPath
      );

      // ✨ normalize
      $relativePath = str_replace('\\', '/', $relativePath);

      // 🔥 شيل public لو موجود
      if (str_starts_with($relativePath, 'public/')) {
        $relativePath = substr($relativePath, 7);
      }

      $relativePath = ltrim($relativePath, '/');

      $files[] = [
        'full' => $fullPath,
        'path' => $relativePath,
      ];
    }

    $this->info("Total files: " . count($files));

    $bar = $this->output->createProgressBar(count($files));
    $bar->start();

    $uploaded = 0;
    $skipped = 0;
    $failed = 0;

    foreach ($files as $file) {

      try {
        $path = $file['path'];

        // skip لو موجود
        if ($ociDisk->exists($path)) {
          $skipped++;
          $bar->advance();
          continue;
        }

        $stream = fopen($file['full'], 'rb');

        if (!$stream) {
          $failed++;
          $bar->advance();
          continue;
        }

        $ociDisk->put($path, $stream);

        if (is_resource($stream)) {
          fclose($stream);
        }

        $uploaded++;

      } catch (\Throwable $e) {
        $failed++;
        $this->error("\nError: {$file['path']} → " . $e->getMessage());
      }

      $bar->advance();
    }

    $bar->finish();

    $this->info("\n\nDone ✅");
    $this->info("Uploaded: $uploaded");
    $this->info("Skipped: $skipped");
    $this->info("Failed: $failed");
  }
}
