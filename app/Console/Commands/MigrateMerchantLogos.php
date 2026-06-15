<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateMerchantLogos extends Command
{
    protected $signature = 'storage:migrate-merchant-logos
                            {--dry-run : Show changes without writing files or DB}
                            {--user= : Migrate a single user ID only}';

    protected $description = 'Move legacy merchant logos (uploads/* and public-disk root) into storage/app/public/{OCI_BUCKET_PREFIX/}shared/merchants/logos/';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $disk = Storage::disk('public');
        $targetDir = merchant_logo_disk_path();

        $query = User::query()->whereNotNull('logo')->where('logo', '!=', '');

        if ($this->option('user')) {
            $query->where('id', $this->option('user'));
        }

        $users = $query->get(['id', 'logo']);
        $migrated = 0;
        $skipped = 0;
        $failed = 0;

        $this->info(sprintf('Processing %d user(s)...', $users->count()));

        foreach ($users as $user) {
            $logo = ltrim($user->logo, '/');

            if (strpos($logo, $targetDir.'/') === 0) {
                $skipped++;
                continue;
            }

            $sourcePath = null;
            $filename = basename($logo);

            if (strpos($logo, 'uploads/') === 0) {
                $sourcePath = public_path($logo);
            } elseif ($disk->exists($logo)) {
                $sourcePath = $disk->path($logo);
            } elseif (is_file(public_path($logo))) {
                $sourcePath = public_path($logo);
            }

            if (! $sourcePath || ! is_file($sourcePath)) {
                $this->warn("  User {$user->id}: file not found for [{$logo}]");
                $failed++;
                continue;
            }

            $newPath = $targetDir.'/'.$filename;

            if ($disk->exists($newPath)) {
                $filename = pathinfo($filename, PATHINFO_FILENAME).'_'.$user->id.'.'.pathinfo($filename, PATHINFO_EXTENSION);
                $newPath = $targetDir.'/'.$filename;
            }

            if ($dryRun) {
                $this->line("  [dry-run] user {$user->id}: {$logo} → {$newPath}");
                $migrated++;
                continue;
            }

            try {
                ensure_merchant_logo_directory();
                $read = fopen($sourcePath, 'r');
                if ($read === false) {
                    throw new \RuntimeException('Could not read source file');
                }
                $disk->writeStream($newPath, $read);
                if (is_resource($read)) {
                    fclose($read);
                }
                $user->update(['logo' => $newPath]);

                if (strpos($logo, 'uploads/') === 0 && is_file($sourcePath)) {
                    @unlink($sourcePath);
                } elseif ($disk->exists($logo) && $logo !== $newPath) {
                    $disk->delete($logo);
                }

                $migrated++;
            } catch (\Throwable $e) {
                $this->error("  User {$user->id}: {$e->getMessage()}");
                $failed++;
            }
        }

        $this->newLine();
        $this->table(
            ['Result', 'Count'],
            [
                ['Migrated / would migrate', $migrated],
                ['Already on canonical merchant logos path', $skipped],
                ['Failed / missing file', $failed],
            ]
        );

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
