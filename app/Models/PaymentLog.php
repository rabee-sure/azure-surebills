<?php

namespace App\Models;

use App\Models\Bill;
use Hashids\Hashids;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;

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

            return true;
        }

        // error message
        if (isset($response['error']) && isset($response['error']['explanation'])) {
            session(['refund_error' => $response['error']['explanation']]);
        } else if (isset($response['response']) && isset($response['response']['gatewayCode'])) {
            session(['refund_error' => $response['response']['gatewayCode']]);
        }

        return false;
    }
}
