<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\File;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class AutoTransferMail extends Mailable
{
    use Queueable, SerializesModels, IsMonitored;

    protected $date;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($date)
    {
        $this->date = $date;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $timestamp = $this->date->timestamp;
        $formate = $this->date->format('l d/m/Y');
        $day = $this->date->format('d-m-Y');

        $fileName = "app/bills/$timestamp/sure_bills_transfers_$day.zip";
        $this->zip($fileName, $timestamp);

        return $this->subject("SureBills Transfers $formate")
            ->view('emails.bills.auto_transfer')
            ->attach(storage_path($fileName));
    }

    /**
     * zipping file.
     *
     * @return $this
     */
    protected function zip($fileName, $timestamp)
    {
        //first delete file
        if(is_file(storage_path($fileName)))
            unlink(storage_path($fileName));

        $zip = new \ZipArchive;
        if ($zip->open(storage_path($fileName), \ZipArchive::CREATE) === TRUE){
            $files = File::files(storage_path("app/bills/$timestamp"));
            foreach ($files as $key => $value) {
                $relativeNameInZipFile = basename($value);
                $zip->addFile($value, $relativeNameInZipFile);
            }
            $zip->close();
        }
    }  
}
