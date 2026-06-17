<?php

namespace App\Jobs;

use App\Models\Transfer;
use App\Models\PaymentLog;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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
    }
}
