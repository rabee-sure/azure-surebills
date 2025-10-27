<?php

namespace App\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
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
    public function __construct( $folder_name, $file_name )
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
    }
}
