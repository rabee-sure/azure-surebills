<?php

namespace App\Jobs;

use App\Mail\TaxInvoiceRequestMail;
use App\Models\TaxInvoiceRequest;
use App\Services\BasicSettingsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendTaxInvoiceRequestMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(BasicSettingsService $basicSettingsService)
    {
<<<<<<< HEAD
        $tax_invoice_requests_emails = $basicSettingsService->get('tax_invoice_requests_emails', '');
=======
        $settings =  Valuestore::make(getSettings());
        $tax_invoice_requests_emails = $settings->get('tax_invoice_requests_emails');
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4
        $emails = explode(",", $tax_invoice_requests_emails);
        if(count($emails)){
            $message = (new TaxInvoiceRequestMail($this->user));
            foreach ($emails as $email) {
                Mail::to($email)->send($message);
            }
        }
    }
}
