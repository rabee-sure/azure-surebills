<?php

namespace App;

use App\Bill;
use App\User;
use App\PaymentLog;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use UsesUuid;

    const VAT_PERCENTAGE = 15;

    public static function deposit($type, PaymentLog $payment)
    {
        $method = 'self::deposit' . ucfirst($type);

        return call_user_func_array($method, [$payment]);
    }

    /**
     * deposit the amount of bill
     *
     * @return void
     */
    protected static function depositBill(PaymentLog $payment)
    {
        $paymentResponse = $payment->results['response'];

        $transaction = new self;
        $transaction->user_id     = $payment->bill->user_id;
        $transaction->bill_id     = $payment->bill->id;
        $transaction->type        = 'credit';
        $transaction->amount      = $paymentResponse['amount'];
        $transaction->reference   = $payment->bill->number;
        $transaction->receipt     = $transaction->generateReceipt();
        $transaction->description = 'Bill ' . $payment->bill->number . ' - ' . $payment->bill->customer_name;
        $transaction->auth_id     = $paymentResponse['resultDetails']['AuthorizeId'];
        $transaction->card_brand  = $paymentResponse['paymentBrand'];
        $transaction->card        = 'XXX' . $paymentResponse['card']['last4Digits'];
        $transaction->balance     = $transaction->user->balance + $transaction->amount;
        $transaction->save();

        // withdraw fees & vat
        call_user_func_array('self::withdrawBillFees', [$payment->bill]);
        call_user_func_array('self::withdrawBillVat', [$payment->bill]);
    }

    /**
     * withdraw payment fess from bill
     *
     * @return void
     */
    protected static function withdrawBillFees(Bill $bill)
    {
        $transaction = new self;
        $transaction->user_id     = $bill->user_id;
        $transaction->bill_id     = $bill->id;
        $transaction->type        = 'debit';
        $transaction->amount      = $bill->payment_fees;
        $transaction->reference   = $bill->number;
        $transaction->receipt     = $transaction->generateReceipt();
        $transaction->description = 'Fee - Transaction Processing';
        $transaction->balance     = $transaction->user->balance - $transaction->amount;
        $transaction->save();
    }

    protected static function withdrawBillVat(Bill $bill)
    {
        $transaction = new self;
        $transaction->user_id     = $bill->user_id;
        $transaction->bill_id     = $bill->id;
        $transaction->type        = 'debit';
        $transaction->amount      = $bill->payment_fees * (self::VAT_PERCENTAGE / 100);
        $transaction->reference   = $bill->number;
        $transaction->receipt     = $transaction->generateReceipt();
        $transaction->description = 'VAT - Transaction Processing';
        $transaction->balance     = $transaction->user->balance - $transaction->amount;
        $transaction->save();
    }

    public function generateReceipt()
    {
        $lastReceipt = self::where('type', $this->type)->orderBy('id', 'desc')->first();

        if ($lastReceipt) {
            return $lastReceipt->receipt + 1;
        }

        if ($this->type == 'credit') {
            return 100000000001;
        } else {
            return 500000000001;
        }
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
     * Get user.
     *
     * @return Collection
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
