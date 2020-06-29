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
        $payment_types = [
            'VISA' => 'DB',
            'MASTER' => 'PA',
            'DISCOVER' => 'PA',
            'AMEX' => 'DB',
            'MADA' => 'PA',
        ];

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
        
        $credit_card = $this->invoice->getDetail('number');
        $this->invoice
            ->detail(['payment_brand' => $this->validatecard($credit_card)])
            ->detail(['payment_type' => $payment_types[$this->validatecard($credit_card)] ]);

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

        if(curl_errno($ch)) {
            throw new PurchaseFailedException('error in Purchase');
        }
        curl_close($ch);

        $body = json_decode($responseData, false);

        dd($body);

        $this->invoice->detail(['cvc' => '***'])
                ->detail(['number' => str_pad(substr($details['number'], -4), strlen($details['number']), '*', STR_PAD_LEFT)]);

        $successPattern = '/(000\.000\.|000\.100\.1|000\.[36])/';
        $success = preg_match($successPattern, $body->result->code);

        $this->invoice->detail(['result_code' => $body->result->code])
            ->detail(['success' => $success])
            ->detail(['result_description' => $body->result->description]);
        $this->invoice->transactionId($body->id ?? "not have id");

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



    protected function validatecard($number)
    {
        $cardtype = array(
            "visa"       => "/^4[0-9]{12}(?:[0-9]{3})?$/",
            "mastercard" => "/^5[1-5][0-9]{14}$/",
            "amex"       => "/^3[47][0-9]{13}$/",
            "discover"   => "/^6(?:011|5[0-9]{2})[0-9]{12}$/",
            "mada"   => "/(4(0(0861|1757|7(197|395)|9201)|1(0685|7633|9593)|2(281(7|8|9)|8(331|67(1|2|3)))|3(1361|2328|4107|9954)|4(0(533|647|795)|5564|6(393|404|672))|5(5(036|708)|7865|8456)|6(2220|854(0|1|2|3))|8(301(0|1|2)|4783|609(4|5|6)|931(7|8|9))|93428)|5(0(4300|8160)|13213|2(1076|4(130|514)|9(415|741))|3(0906|1095|2013|5(825|989)|6023|7767|9931)|4(3(085|357)|9760)|5(4180|7606|8848)|8(5265|8(8(4(5|6|7|8|9)|5(0|1))|98(2|3))|9(005|206)))|6(0(4906|5141)|36120)|9682(0(1|2|3|4|5|6|7|8|9)|1(0|1)))\d{10}$/",
        );

        if (preg_match($cardtype['mada'],$number))
        {
            return 'MADA';
        }else if (preg_match($cardtype['visa'],$number)){
            return 'VISA';
        }else if (preg_match($cardtype['mastercard'],$number))
        {
            return 'MASTER';
        }else if (preg_match($cardtype['amex'],$number))
        {
            return 'AMEX';
        }else if (preg_match($cardtype['discover'],$number))
        {
            return 'DISCOVER';        
        }else{
            return false;
        } 
    }
}