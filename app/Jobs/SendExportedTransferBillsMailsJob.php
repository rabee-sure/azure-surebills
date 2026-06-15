<?php

namespace App\Jobs;

use App\Mail\TransferBillsExportedExcelMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendExportedTransferBillsMailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var string Relative path on the public disk (OCI when enabled), e.g. shared/exports/transfers/bills/… */
    protected $exportStoragePath;

    /** @var array<int, string>|string */
    protected $email;

    /**
     * @param  array<int, string>|string  $email
     */
    public function __construct(string $exportStoragePath, $email)
    {
        $this->exportStoragePath = $exportStoragePath;
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->email)->send(new TransferBillsExportedExcelMail($this->exportStoragePath));
    }
}
