<?php

namespace App\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
<<<<<<< HEAD
=======
use Illuminate\Support\Facades\File;
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4
use Illuminate\Support\Facades\Storage;

class ZipFolderJob
{
    use Dispatchable, SerializesModels;

    protected $folder_name;

    protected $file_name;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($folder_name, $file_name)
    {
        $this->folder_name = $folder_name;
        $this->file_name = $file_name;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
<<<<<<< HEAD
        $disk = Storage::disk('public');
        $folder = trim($this->folder_name, '/');
        $relativeZip = $folder.'/'.$this->file_name;

        if ($disk->exists($relativeZip)) {
            $disk->delete($relativeZip);
        }

        $tempLocal = storage_path('app/temp-zip-'.uniqid('', true).'-'.$this->file_name);
        @mkdir(dirname($tempLocal), 0755, true);

        $zip = new \ZipArchive;
        if ($zip->open($tempLocal, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return;
        }

        foreach ($disk->files($folder) as $path) {
            if ($path === $relativeZip) {
                continue;
            }
            $zip->addFromString(basename($path), $disk->get($path));
        }
        $zip->close();

        if (is_file($tempLocal)) {
            $stream = fopen($tempLocal, 'r');
            if ($stream !== false) {
                $disk->writeStream($relativeZip, $stream);
                if (is_resource($stream)) {
                    fclose($stream);
                }
            }
            @unlink($tempLocal);
        }
=======
        $zipFileName = "{$this->file_name}";
        $zipPath = "{$this->folder_name}/{$zipFileName}";
        $localZipPath = storage_path("app/tmp_{$zipFileName}");
        if (Storage::exists($zipPath)) {
            Storage::delete($zipPath);
        }
        $zip = new ZipArchive;
        if ($zip->open($localZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $files = Storage::allFiles($this->folder_name);
            foreach ($files as $filePath) {
                if (basename($filePath) === $zipFileName) {
                    continue;
                }
                $fileContent = Storage::get($filePath);
                $zip->addFromString(basename($filePath), $fileContent);
            }
            $zip->close();
        }
        Storage::put($zipPath, file_get_contents($localZipPath));
        @unlink($localZipPath);
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4
    }
}
