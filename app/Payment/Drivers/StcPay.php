<?php

namespace App\Payment\Drivers;

use App\Exceptions\InvalidPaymentException;
use App\Exceptions\PurchaseFailedException;
use App\Payment\Abstracts\Driver;
use App\Payment\Contracts\ReceiptInterface;
use App\Payment\Invoice;
use GuzzleHttp\Client;

class StcPay extends Driver
{
    /**
     * StcPay Client.
     *
     * @var object
     */
    protected $client;

    /**
     * Invoice
     *
     * @var Invoice
     */
    protected $invoice;

    /**
     * Driver settings
     *
     * @var object
     */
    protected $settings;

    public function __construct(Invoice $invoice, $settings)
    {
        $this->invoice($invoice); // Set the invoice.
        $this->settings = (object) $settings;
        $this->client = new Client();
    }

    // Purchase the invoice, save its transactionId and finaly return it.
    public function purchase()
    {
        // STC purchase integration is intentionally stubbed until configured.
        throw new PurchaseFailedException('STC Pay purchase is not implemented.');
    }

    // Redirect into bank using transactionId, to complete the payment.
    public function pay()
    {
        // It is better to set bankApiUrl in config/payment.php and retrieve it here:
        $bankUrl = $this->settings->bankApiUrl; // bankApiUrl is the config name.

        // Prepare payment url.
        $payUrl = $bankUrl.$this->invoice->getTransactionId();

        // Redirect to the bank.
        return redirect()->to($payUrl);
    }

    // Verify the payment (we must verify to ensure that user has paid the invoice).
    public function verify(): ReceiptInterface
    {
        // STC verify integration is intentionally stubbed until configured.
        throw new InvalidPaymentException('STC Pay verification is not implemented.');
    }
}
