<?php

namespace App\Payment\Drivers;

use App\Exceptions\InvalidPaymentException;
use App\Exceptions\PurchaseFailedException;
use App\PaymentLog;
use App\Payment\Abstracts\Driver;
use App\Payment\Contracts\ReceiptInterface;
use App\Payment\Invoice;
use App\Payment\Receipt;
use GuzzleHttp\Client;

class HyperPay extends Driver
{
    /**
     * HyperPay Client.
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

    /**
     * HyperPay constructor.
     * Construct the class with the relevant settings.
     *
     * @param Invoice $invoice
     * @param $settings
     */
    public function __construct(Invoice $invoice, $settings)
    {
        $this->invoice($invoice);
        $this->settings = (object) $settings;
        $this->client = new Client();
    }

    /**
     * Purchase Invoice.
     *
     * @return string
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function purchase()
    {
        $details = $this->invoice->getDetails();

        $orderId = crc32($this->invoice->getUuid()).time();
        if (!empty($details['orderId'])) {
            $orderId = $details['orderId'];
        } elseif (!empty($details['order_id'])) {
            $orderId = $details['order_id'];
        }

        $mobile = null;
        if (!empty($details['mobile'])) {
            $mobile = $details['mobile'];
        } elseif (!empty($details['phone'])) {
            $mobile = $details['phone'];
        }

        $details = $this->invoice->getDetails();

        $url = $this->settings->api_purchase_url;
        $data = "entityId=".$this->settings->entity_id .
                "&amount=".$this->invoice->getAmount().
                "&currency=SAR" .
                "&paymentBrand=".$details['payment_brand'] .
                "&paymentType=".$details['payment_type'] .
                "&card.number=".$details['number'] .
                "&card.holder=".$details['name'] .
                "&card.expiryMonth=".$details['expiry_month'] .
                "&card.expiryYear=".$details['expiry_year'] .
                "&card.cvv=".$details['cvc'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                       'Authorization:Bearer '.$this->settings->access_token));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);

        PaymentLog::create([
            'user_id' => auth()->user()->id,
            'results' => $responseData,
            'status' => 1,
        ]);
        if(curl_errno($ch)) {
            PaymentLog::create([
                'user_id' => auth()->user()->id,
                'results' => $responseData,
                'status' => 2,
            ]);
            throw new PurchaseFailedException('error in Purchase');
        }
        curl_close($ch);

        $body = json_decode($responseData, false);

        if(isset($body->result->parameterErrors)) {
            $first_error = $body->result->parameterErrors[0];
            throw new PurchaseFailedException($first_error->name .' - '. $first_error->message);
        }


        $this->invoice->transactionId($body->id);

        // return the transaction's id
        return $this->invoice->getTransactionId();
    }

    /**
     * Pay the Invoice
     *
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function pay()
    {
        $payUrl = $this->settings->apiPaymentUrl.$this->invoice->getTransactionId();

        if (strtolower($this->settings->mode) == 'direct') {
            $payUrl .= '/direct';
        }

        // redirect using laravel logic
        return redirect()->to($payUrl);
    }

    /**
     * Verify payment
     *
     * @return mixed|void
     *
     * @throws InvalidPaymentException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function verify() : ReceiptInterface
    {
        $successFlag = request()->input('success');
        $orderId = request()->input('orderId');
        $transactionId = $this->invoice->getTransactionId() ?? request()->input('trackId');

        if ($successFlag != 1) {
            $this->notVerified('پرداخت با شکست مواجه شد');
        }

        //start verfication
        $data = array(
            "merchant" => $this->settings->merchantId, //required
            "trackId" => $transactionId, //required
        );

        $response = $this->client->request(
            'POST',
            $this->settings->apiVerificationUrl,
            ["json" => $data, "http_errors" => false]
        );


        $body = json_decode($response->getBody()->getContents(), false);

        if ($body->result != 100) {
            $this->notVerified($body->message);
        }

        /*
            for more info:
            var_dump($body);
        */

        return $this->createReceipt($orderId);
    }

    /**
     * Generate the payment's receipt
     *
     * @param $referenceId
     *
     * @return Receipt
     */
    protected function createReceipt($referenceId)
    {
        $receipt = new Receipt('HyperPay', $referenceId);

        return $receipt;
    }

    /**
     * Trigger an exception
     *
     * @param $message
     * @throws InvalidPaymentException
     */
    private function notVerified($message)
    {
        if (empty($message)) {
            throw new InvalidPaymentException('خطای ناشناخته ای رخ داده است.');
        } else {
            throw new InvalidPaymentException($message);
        }
    }
}