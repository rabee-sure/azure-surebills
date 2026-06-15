<?php

namespace App\Jobs;

use App\Mail\MerchantBillsExportedExcelMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendExportedMerchantBillsMailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var string Full relative path on the public disk (OCI when enabled), e.g. shared/exports/merchants/bills/… */
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
        Mail::to($this->email)->send(new MerchantBillsExportedExcelMail($this->exportStoragePath));
    }
}
