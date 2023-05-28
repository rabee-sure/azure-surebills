<?php

namespace App\Jobs;

use App\Exports\VerifiedMerchantExportData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class ExportVerifiedMerchantsJob
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $file_name = 'verified_merchants.xlsx';
        return (new VerifiedMerchantExportData())->store($filePath = 'verified-merchants/'. $file_name);
    }
}
