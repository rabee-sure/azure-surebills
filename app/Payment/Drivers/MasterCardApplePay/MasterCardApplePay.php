<?php

namespace App\Payment\Drivers\MasterCardApplePay;

use App\PaymentLog;
use GuzzleHttp\Client;
use App\Payment\Invoice;
use App\Payment\Receipt;
use App\Payment\Abstracts\Driver;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Exceptions\InvalidPaymentException;
use App\Exceptions\PurchaseFailedException;
use App\Payment\Contracts\ReceiptInterface;
use URL;

class MasterCardApplePay extends Driver
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
        $locale = App::getLocale();
        $details = $this->invoice->getDetails();
        $resultUrl = route('bills.handle', ['hash' => $details['hash']]);

        require 'payment_page.php';
        dd('aa');
        ?>
        <button lang="<?php echo $locale ?>" style="-webkit-appearance: -apple-pay-button; -apple-pay-button-type: buy;"></button>
        <script><?php require 'appr.js'; ?></script>
        <script><?php require 'script.js'; ?></script>
        <?php
        dd('aa');
        $client = new Client();
        $response = $client->put(
            config('payment.drivers.mastercard_applepay.api_base_url').'/order/'.$details['surebills_payment_log_id'].'/transaction/aa',
            [
                'json' => ['session' => ['authenticationLimit' => 25],],
                'auth' => [config('payment.drivers.mastercard_iframe.operator_username'), config('payment.drivers.mastercard_iframe.operator_password')],
                // 'json' => ["apiOperation" => "CREATE_CHECKOUT_SESSION", "interaction" => [ "operation" => "PURCHASE"],
                //             "order" => ["amount" => $details['bill']['total'], "currency" => "SAR", "description" => "Invoice number: ".$details['bill']['number'],
                //                 "id" => $details['bill']['id']]]
            ]
        );
        $body = json_decode($response->getBody()->getContents(), false);
        dd($body);
        // $sessionResponse = $client->get(config('payment.drivers.mastercard_iframe.api_base_url').'/session/'.$body->session->id,
        //                             ['auth' => [config('payment.drivers.mastercard_iframe.operator_username'), config('payment.drivers.mastercard_iframe.operator_password')]]);
        // $sessionBody = json_decode($sessionResponse->getBody()->getContents(), false);
        // dd($sessionBody);

        if(\Request::segment(5) == 'en')
        {
            $locale = 'en_us';
        }

        $script = '<script>';
        $script .= 'Checkout.configure({';
        $script .= 'session: {id: "'.$body->session->id.'"},';
        $script .= 'merchant: "'.$this->settings->merchant_id.'",';
        $script .= 'order: {walletProvider:"APPLE_PAY", amount: '.$details['bill']['total'].', currency: "SAR", description: "Invoice number: '.$details['bill']['number'].'", reference:"'.$details['bill']['id'].'"},';
        $script .= 'interaction: {operation: "PURCHASE", merchant: {name: "'.$details['bill']['business_name'].'"}, displayControl: {billingAddress: "HIDE", orderSummary: "HIDE"}, locale: "'.$locale.'"},';
        // $script .= 'sourceOfFunds: {provided: {card: {devicePayment: {paymentToken: "PKPaymentToken.paymentData"}}}';
        $script .= '});';
        $script .= 'Checkout.showLightbox(); </script>';
        $script .= '<form action="'.$resultUrl.'" method="GET" class="mastercardPaymentWidgets" data-brands="VISA MASTER MADA">';
        $script .= '<input type="hidden" name="sessionId" value="'.$body->session->id.'" /></form>';
        return $script;
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
        $client = new Client();

        $sessionResponse = $client->get(config('payment.drivers.mastercard_iframe.api_base_url').'/session/'.request()->sessionId,
                                    ['auth' => [config('payment.drivers.mastercard_iframe.operator_username'), config('payment.drivers.mastercard_iframe.operator_password')]]);
        $sessionBody = json_decode($sessionResponse->getBody()->getContents(), false);

        $orderResponse = $client->get(config('payment.drivers.mastercard_iframe.api_base_url').'/order/'.$sessionBody->order->id,
                                    ['auth' => [config('payment.drivers.mastercard_iframe.operator_username'), config('payment.drivers.mastercard_iframe.operator_password')]]);
        $orderBody = json_decode($orderResponse->getBody()->getContents(), false);

        $orderResponseJson['id'] = $orderBody->id;
        $orderResponseJson['card']['bin'] = '';
        $orderResponseJson['card']['holder'] = $orderBody->sourceOfFunds->provided->card->nameOnCard;
        $orderResponseJson['card']['binCountry'] = '';
        $orderResponseJson['card']['expiryYear'] = $orderBody->sourceOfFunds->provided->card->expiry->year;
        $orderResponseJson['card']['expiryMonth'] = $orderBody->sourceOfFunds->provided->card->expiry->month;
        $orderResponseJson['card']['last4Digits'] = substr($orderBody->sourceOfFunds->provided->card->number, -4);
        $orderResponseJson['result']['code'] = $orderBody->transaction[0]->response->acquirerCode;
        $orderResponseJson['result']['description'] = $orderBody->transaction[0]->result;
        $orderResponseJson['paymentType'] = '';
        $orderResponseJson['paymentBrand'] = $orderBody->sourceOfFunds->provided->card->brand;
        $orderResponseJson['merchantTransactionId'] = $details['bill']['id'];

        $this->invoice->detail(['result_code' => $orderResponseJson['result']['code']])
            ->detail(['success' => $orderResponseJson['result']['code'] == 00? 1:0])
            ->detail(['response' => $orderResponseJson])
            ->detail(['description' => $orderResponseJson['result']['description']])
            ->detail(['gateway' => 'mastercard'])
            ->detail(['gateway_response' => $orderBody]);
        $this->invoice->transactionId(request()->sessionId ?? "not have id");
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
        return null;
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
