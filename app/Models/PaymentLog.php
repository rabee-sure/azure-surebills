<?php

namespace App\Models;

use App\Models\Bill;
use App\Services\CyberSourceService;
use App\Services\MasterCardSandboxSimulator;
use App\Services\MasterCardService;
use Hashids\Hashids;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use App\Jobs\MastercardWebhookSimulation;

class PaymentLog extends Model
{
    protected $fillable = [
        'id',
        'user_id',
        'bill_id',
        'payment_method',
        'results',
        'status',
        'data',
        'provider_name',
    ];

    protected $casts = [
        'results'   =>  'array',
        'data'   =>  'array',
    ];

    public function scopeUserId($query, $value)
    {
        return $query->where('user_id', $value);
    }

    public function getHashIdAttribute()
    {
        $hashids = new Hashids();
        return $hashids->encodeHex($this->id);
    }

    static public function decodeId($hashed_id)
    {
        $hashids = new Hashids();
        $id = $hashids->decodeHex($hashed_id);

        return self::find($id);
    }

    /**
     * Get bill.
     *
     * @return Collection
     */
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    /**
     * Get bill.
     *
     * @return Collection
     */
    public function refund($amount)
    {
        \Log::channel('refunded_transactions')->info("refunded transaction from paymentLog model in refund method", array($this->bill->id, $amount));

        if ($amount > $this->bill->total) {
            session(['refund_error' => __('Amount is more than bill paid amount.')]);
            return false;
        }

        // new log
        $payment = PaymentLog::create([
            'bill_id'        => $this->bill->id,
            'payment_method' => 'mastercard_refund',
            'results'        => [],
            'data'           => [],
            'status'         => 0,
        ]);


        // check for bill paymentLog
        $billPaymentLog = PaymentLog::where('bill_id', $this->bill->id)
        ->whereIn('payment_method', ['mastercard_pay', 'hyperpay_applepay', 'mastercard_applepay', 'stc_pay'])
        ->where('webhook_response_received', 1)
        ->where('is_failure', 0)
        ->orderBy('created_at', 'Desc')
        ->first();

        if($billPaymentLog != null){
            if($billPaymentLog->provider_name == 'cybersource'){
                $cyberSourceService = new CyberSourceService;
                return $cyberSourceService->processRefund($this->bill, $payment, $amount);
            }elseif($billPaymentLog->provider_name == 'mastercard'){
                // SANDBOX PAYMENT SIMULATION FOR REFUND (no real MPGS calls)
                // Check simulation flag directly from config (more reliable than function_exists)
                $simulationEnabled = !app()->environment('production') 
                    && (bool) config('mastercard.payment_simulation', false);

                \Log::channel('refunded_transactions')->info("Refund check - simulation enabled: " . ($simulationEnabled ? 'YES' : 'NO'), [
                    'environment' => app()->environment(),
                    'config_value' => config('mastercard.payment_simulation'),
                    'bill_id' => $this->bill->id
                ]);

                if ($simulationEnabled) {
                    /** @var MasterCardSandboxSimulator $simulator */
                    $simulator = app(MasterCardSandboxSimulator::class);
                    $fakeResponse = $simulator->simulateSuccessfulRefund($this->bill, $payment, $amount);

                    \Log::channel('refunded_transactions')->info("Refund SIMULATION enabled - skipping MPGS API call", [
                        'bill_id' => $this->bill->id,
                        'refund_amount' => $amount,
                        'simulated_response' => $fakeResponse
                    ]);

                    // Save simulated refund response on the refund log
                    $payment->results = $fakeResponse;
                    $payment->refunded_amount = $amount;
                    $payment->save();

                    // Update total refunded amount on original payment log
                    $this->refunded_amount += $amount;
                    $this->save();

                    /** @var MasterCardService $masterCardService */
                    $masterCardService = app(MasterCardService::class);
                    $masterCardService->handleRefundTransaction($fakeResponse, $this->bill, $payment);

                    return true;
                }

                // REAL MPGS REFUND API CALL (only when simulation is disabled)
                \Log::channel('refunded_transactions')->info("Calling REAL MPGS REFUND API", [
                    'bill_id' => $this->bill->id,
                    'transaction_id' => $payment->id,
                    'amount' => $amount,
                    'simulation_enabled' => $simulationEnabled ?? false
                ]);
                
                // api link
                $link = config('payment.drivers.mastercard.base_url').'/api/rest/version/58/merchant/'.config('payment.drivers.mastercard.merchant_id').'/order/'.$this->bill->id.'/transaction/'.$payment->id;
                $client = new Client(['http_errors' => false]);
                $response = $client->put($link,
                    [
                        'json' => [
                            'apiOperation' => 'REFUND',
                            'transaction' => [
                                'amount'   => number_format($amount, 2, '.', ''),
                                'currency' => 'SAR'
                            ]
                        ],
                        'auth' => [
                            config('payment.drivers.mastercard.operator_username'),
                            config('payment.drivers.mastercard.operator_password')
                        ],
                    ]
                );
                $response = json_decode($response->getBody()->getContents(), true);
                \Log::channel('refunded_transactions')->info("Refund API rescponse", $response);
                $payment->results = $response;
                $payment->refunded_amount = $amount;
                $payment->save();

                
                if (isset($response['response']) && isset($response['response']['gatewayCode']) && $response['response']['gatewayCode'] == 'APPROVED') {
                    \Log::channel('refunded_transactions')->info("refunded transaction from mastercard rescponse", array(
                        "bill_id" => $this->bill->id, 
                        "total_refunded_amount" => $response['order']['totalRefundedAmount'],
                        "transaction_amount" => $response['transaction']['amount']
                    ));
                    // update refunded amount
                    $this->refunded_amount += $amount;
                    $this->save();

                    MastercardWebhookSimulation::dispatch($this->bill->id, $payment->id)->delay(now()->addMinutes(config('mastercard.webhook_simulation_delay_in_minutes')));

                    return true;
                }

                // error message
                if (isset($response['error']) && isset($response['error']['explanation'])) {
                    session(['refund_error' => $response['error']['explanation']]);
                    $payment->is_failure = true;
                    $payment->save();
                } else if (isset($response['response']) && isset($response['response']['gatewayCode'])) {
                    session(['refund_error' => $response['response']['gatewayCode']]);
                    $payment->is_failure = true;
                    $payment->save();
                }

                return false;
            }
        } else {
            return false;
        }
    }
}
