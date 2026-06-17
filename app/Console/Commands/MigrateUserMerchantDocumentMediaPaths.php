<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Support\Storage\ExportStoragePaths;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Move User business_documents / bank_documents blobs to admin-aligned keys:
 *   {prefix}shared/merchants/{business|bank}_documents/{model_id}/{file_name}
 *
 * Sources tried (first existing wins): legacy {media_id}/…, old {root}/{media_id}/…, flat {root}/{file_name}.
 */
class MigrateUserMerchantDocumentMediaPaths extends Command
{
    protected $signature = 'media:migrate-user-merchant-documents {--dry-run : Show planned copies without writing}';

    protected $description = 'Copy User KYC media files to shared/merchants/{business|bank}_documents/{userId}/ on the public disk';

    public function handle(): int
    {
        $mediaClass = config('media-library.media_model', Media::class);
        $disk = Storage::disk('public');
        $dry = (bool) $this->option('dry-run');

        $query = $mediaClass::query()
            ->where('model_type', User::class)
            ->whereIn('collection_name', ['business_documents', 'bank_documents'])
            ->orderBy('id');

        $count = (clone $query)->count();
        if ($count === 0) {
            $this->info('No media rows to migrate.');

            return 0;
        }

        $this->info("Found {$count} media row(s).");

        foreach ($query->cursor() as $media) {
            /** @var \Spatie\MediaLibrary\MediaCollections\Models\Media $media */
            $root = $media->collection_name === 'business_documents'
                ? ExportStoragePaths::merchantBusinessDocumentsRoot()
                : ExportStoragePaths::merchantBankDocumentsRoot();

            $userId = (int) $media->model_id;
            $newMain = $root.'/'.$userId.'/'.$media->file_name;

            if ($disk->exists($newMain)) {
                $this->line("Skip #{$media->id} (destination exists): {$newMain}");

                continue;
            }

            $mid = (string) $media->getKey();
            $candidates = [
                $mid.'/'.$media->file_name,
                $root.'/'.$mid.'/'.$media->file_name,
                $root.'/'.$media->file_name,
            ];

            $source = null;
            foreach (array_unique($candidates) as $candidate) {
                if ($disk->exists($candidate)) {
                    $source = $candidate;
                    break;
                }
            }

            if ($source === null) {
                $this->warn("No source file found for media #{$media->id} ({$media->collection_name}) {$media->file_name}");

                continue;
            }

            if ($dry) {
                $this->line("[dry-run] {$source} -> {$newMain}");
            } else {
                $disk->makeDirectory($root.'/'.$userId);
                $disk->copy($source, $newMain);
                if ($source !== $newMain) {
                    $disk->delete($source);
                }
                $this->line("Migrated media #{$media->id} main file.");
            }

            $this->migrateSubdir($disk, $dry, $mid, $root, $userId, 'conversions');
            $this->migrateSubdir($disk, $dry, $mid, $root, $userId, 'responsive-images');

            if (! $dry) {
                if ($disk->exists($mid)) {
                    $disk->deleteDirectory($mid);
                }
                $nested = $root.'/'.$mid;
                if ($disk->exists($nested)) {
                    $disk->deleteDirectory($nested);
                }
            }
        }

        $this->info($dry ? 'Dry run complete.' : 'Migration complete.');

        return 0;
    }

    /**
     * @param  \Illuminate\Contracts\Filesystem\Filesystem  $disk
     */
    protected function migrateSubdir($disk, bool $dry, string $mediaId, string $root, int $userId, string $subdir): void
    {
        $oldPrefixes = [
            $mediaId.'/'.$subdir,
            $root.'/'.$mediaId.'/'.$subdir,
        ];
        $newBase = $root.'/'.$userId.'/'.$subdir;

        foreach ($oldPrefixes as $oldBase) {
            if (! $disk->exists($oldBase)) {
                continue;
            }
            foreach ($disk->allFiles($oldBase) as $path) {
                $suffix = substr($path, strlen($oldBase));
                $suffix = ltrim($suffix, '/');
                $newPath = $newBase.($suffix !== '' ? '/'.$suffix : '');
                if ($dry) {
                    $this->line("[dry-run] {$path} -> {$newPath}");
                } else {
                    if (! $disk->exists($newPath)) {
                        $disk->makeDirectory(dirname($newPath));
                        $disk->copy($path, $newPath);
                    }
                    $disk->delete($path);
                }
            }
        }
    }
}
