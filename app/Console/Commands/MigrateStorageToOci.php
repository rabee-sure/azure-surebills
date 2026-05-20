<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateStorageToOci extends Command
{
    protected $signature = 'storage:migrate-to-oci
                            {--dry-run : List files without uploading}
                            {--path= : Only migrate files under this subdirectory}
                            {--delete-local : Remove local copy after successful upload}';

    protected $description = 'Copy existing local public files to OCI Object Storage (zero-downtime migration helper)';

    public function handle(): int
    {
        if (! oci_storage_enabled()) {
            $this->error('OCI_ENABLED is false. Enable OCI in .env before running this command.');

            return self::FAILURE;
        }

        $local = Storage::disk('public-local');
        $remote = Storage::disk('oci');
        $prefix = $this->option('path') ? trim($this->option('path'), '/').'/' : '';
        $dryRun = (bool) $this->option('dry-run');
        $deleteLocal = (bool) $this->option('delete-local');

        $files = $local->allFiles($prefix ? rtrim($prefix, '/') : null);
        $uploaded = 0;
        $skipped = 0;
        $failed = 0;

        $this->info(sprintf('Found %d local file(s) to process.', count($files)));

        $bar = $this->output->createProgressBar(count($files));
        $bar->start();

        foreach ($files as $file) {
            $bar->advance();

            if ($remote->exists($file)) {
                $skipped++;
                continue;
            }

            if ($dryRun) {
                $this->line('');
                $this->line("  [dry-run] would upload: {$file}");
                $uploaded++;
                continue;
            }

            try {
                $stream = $local->readStream($file);
                $remote->writeStream($file, $stream);
                if (is_resource($stream)) {
                    fclose($stream);
                }
                $uploaded++;

                if ($deleteLocal) {
                    $local->delete($file);
                }
            } catch (\Throwable $e) {
                $failed++;
                $this->line('');
                $this->error("  Failed: {$file} — {$e->getMessage()}");
            }
        }

        $bar->finish();
        $this->newLine(2);
        $this->table(
            ['Metric', 'Count'],
            [
                ['Uploaded / would upload', $uploaded],
                ['Skipped (already on OCI)', $skipped],
                ['Failed', $failed],
            ]
        );

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
