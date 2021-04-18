<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;

class RequestTransferMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $date;

    protected $user;

    protected $transfer;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($date, $user, $transfer)
    {
        $this->date = $date;
        $this->user = $user;
        $this->transfer = $transfer;
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
        return $this->subject( $this->user->business_name ." requesting a new transfer - SureBills Transfers")
            ->view('emails.bills.request_transfer', [
                'user' => $this->user,
                'transfer' => $this->transfer,
            ])
            // ->attach(storage_path($fileName))
            ;
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
