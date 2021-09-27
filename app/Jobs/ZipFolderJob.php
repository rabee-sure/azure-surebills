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
use Illuminate\Support\Facades\File;

class ZipFolderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


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
        $file_full_path = 'app/public/'.$this->folder_name.'/'.$this->file_name;
        //first delete file
        if(is_file(storage_path($file_full_path)))
            unlink(storage_path($file_full_path));

        $zip = new \ZipArchive;
        if ($zip->open(storage_path($file_full_path), \ZipArchive::CREATE) === TRUE){
            $files = File::files(storage_path("app/public/$this->folder_name"));
            foreach ($files as $key => $value) {
                $relativeNameInZipFile = basename($value);
                $zip->addFile($value, $relativeNameInZipFile);
            }
            $zip->close();
        }

    }
}
