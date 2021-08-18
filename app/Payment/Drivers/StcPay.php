<?php

namespace App\Payment\Drivers;

use App\Exceptions\InvalidPaymentException;
use App\Exceptions\PurchaseFailedException;
use App\Models\PaymentLog;
use App\Payment\Abstracts\Driver;
use App\Payment\Contracts\ReceiptInterface;
use App\Payment\Invoice;
use App\Payment\Receipt;
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
        // Request for a payment transaction id.
        ...
            
        $this->invoice->transactionId($transId);
        
        return $transId;
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
        $verifyPayment = $this->settings->verifyApiUrl;
        
        $verifyUrl = $verifyPayment.$this->invoice->getTransactionId();
        
        ...
        
        /**
			Then we send a request to $verifyUrl and if payment is not valid we throw an InvalidPaymentException with a suitable message.
        **/
        throw new InvalidPaymentException('a suitable message');
        
        /**
        	We create a receipt for this payment if everything goes normally.
        **/
        return new Receipt('stcpay', 'payment_receipt_number');
    }
}