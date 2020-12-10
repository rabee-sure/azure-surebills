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


    /**
     * generate Receipt for bill.
     *
     * @return Int
     */
    public function generateReceipt()
    {
        $lastReceipt = self::where('type', $this->type)->orderBy('receipt', 'desc')->first();

        if ($lastReceipt)
            return $lastReceipt->receipt + 1;

        if ($this->type == 'credit')
            return 100000000001;
        else
            return 500000000001;
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

    /**
     * boot method.
     */
    public static function boot()
    {
        parent::boot();
        static::creating(function($trans)
        {
            $trans->receipt = $trans->generateReceipt();
            $trans->balance = ($trans->type == 'debit') ? $trans->user->balance - $trans->amount : $trans->user->balance + $trans->amount;
        });
    }
}
