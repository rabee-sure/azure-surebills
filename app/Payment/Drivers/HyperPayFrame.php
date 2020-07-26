<?php

namespace App\Payment\Drivers;

use App\PaymentLog;
use GuzzleHttp\Client;
use App\Payment\Invoice;
use App\Payment\Receipt;
use App\Payment\Abstracts\Driver;
use Illuminate\Support\Facades\App;
use App\Exceptions\InvalidPaymentException;
use App\Exceptions\PurchaseFailedException;
use App\Payment\Contracts\ReceiptInterface;

class HyperPayFrame extends Driver
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
    public function generateIframe()
    {
        $details = $this->invoice->getDetails();

        $url  = $this->settings->api_base_url . '/checkouts';
        $data = "entityId=".$this->settings->entity_id .
                "&amount=".$this->invoice->getAmount().
                "&currency=SAR" .
                "&merchantTransactionId=" . $details['bill']['id'] .
                "&customer.email=" . $details['bill']['customer_email'] .
                "&paymentType=DB";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                       'Authorization:Bearer '.$this->settings->access_token));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, app()->environment('production') ? true : false);// this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);

        if(curl_errno($ch)) {
            throw new PurchaseFailedException('error in Purchase');
        }
        curl_close($ch);

        $response = json_decode($responseData, false);

        $resultUrl = route('bills.handle', ['hash' => $details['hash']]);;

        return  '<script>
                    var wpwlOptions = {
                        // locale: "'.App::getLocale().'",
                        style: "plain",
                        showCVVHint: true,
                        brandDetection: true,
                        onReady: function(){ 
                            $(".wpwl-group-cardNumber").after($(".wpwl-group-brand").detach());
                            $(".wpwl-group-cvv").after( $(".wpwl-group-cardHolder").detach());
                            var visa = $(".wpwl-brand:first").clone().removeAttr("class").attr("class", "wpwl-brand-card wpwl-brand-custom wpwl-brand-VISA")
                            var master = $(visa).clone().removeClass("wpwl-brand-VISA").addClass("wpwl-brand-MASTER");
                            $(".wpwl-brand:first").after( $(master)).after( $(visa));
                        },
                        onChangeBrand: function(e){
                            $(".wpwl-brand-custom").css("opacity", "0.3");
                            $(".wpwl-brand-" + e).css("opacity", "1");
                        }
                    }
                </script>

                <script src="'.$this->settings->api_base_url.'/paymentWidgets.js?checkoutId='.$response->id.'"></script>

                <form action="'.$resultUrl.'" method="POST" class="paymentWidgets" data-brands="VISA MASTER MADA"></form>';
    }

    /**
     * Purchase Invoice.
     *
     * @return string
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function paymentStatus()
    {
        $details = $this->invoice->getDetails();

        $url  = $this->settings->api_base_url . '/checkouts/' . $details['payment_id'] . '/payment';
        $url .= "?entityId=".$this->settings->entity_id;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                       'Authorization:Bearer '.$this->settings->access_token));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, app()->environment('production') ? true : false);// this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);

        if(curl_errno($ch)) {
            throw new PurchaseFailedException('error in Purchase');
        }
        curl_close($ch);

        $response = json_decode($responseData, false);

        
        // check if success
        $successPattern = '/(000\.000\.|000\.100\.1|000\.[36])/';
        $success = preg_match($successPattern, $response->result->code);

        // update invoice status
        $this->invoice->detail(['result_code' => $response->result->code])
            ->detail(['success' => $success])
            ->detail(['response' => json_decode($responseData, true)])
            ->detail(['description' => $response->result->description])
            ->detail(['gateway' => 'hyperpay'])
            ->detail(['gateway_response' => $response]);
        $this->invoice->transactionId($response->id ?? "not have id");
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
        dd('aa');
        $payment_types = [
            'VISA' => 'DB',
            'MASTER' => 'DB',
            'DISCOVER' => 'DB',
            'AMEX' => 'DB',
            'MADA' => 'DB',
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

        $url = $this->settings->api_base_url . '/checkouts';
        $data = "entityId=".$this->settings->entity_id .
                "&amount=".$this->invoice->getAmount().
                "&currency=SAR" .
                "&paymentBrand=".$details['payment_brand'] .
                "&paymentType=" . $details['payment_type'] .
                "&card.number=".$details['number'] .
                "&card.holder=".$details['name'] .
                "&card.expiryMonth=".$details['expiry_month'] .
                "&card.expiryYear=".$details['expiry_year'] .
                "&card.cvv=".$details['cvc'].
                "&shopperResultUrl=".urlencode(route('bills.handle', ['id' => $details['bill']['id']])) .
                "&notificationUrl=".urlencode(route('bills.handle', ['id' => $details['bill']['id']])) .
                "&merchantTransactionId=" . $details['bill']['id'] . now() .
                "&customer.email=" . $details['bill']['customer_email'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                       'Authorization:Bearer '.$this->settings->access_token));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, app()->environment('production') ? true : false);// this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);

        if(curl_errno($ch)) {
            throw new PurchaseFailedException('error in Purchase');
        }
        curl_close($ch);

        $body = json_decode($responseData, false);

        $this->invoice->detail(['cvc' => '***'])
                ->detail(['number' => str_pad(substr($details['number'], -4), strlen($details['number']), '*', STR_PAD_LEFT)]);

        $successPattern = '/(000\.000\.|000\.100\.1|000\.[36])/';
        $success = preg_match($successPattern, $body->result->code);
        $pendingPattern = '/(000\.200\.)/';
        $pending = preg_match($pendingPattern, $body->result->code);

        $this->invoice->detail(['result_code' => $body->result->code])
            ->detail(['success' => $success])
            ->detail(['pending' => $pending])
            ->detail(['redirect' => $body->redirect ?? null])
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
}
