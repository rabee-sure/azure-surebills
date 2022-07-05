<?php

namespace App\Models;

use App\Models\Bill;
use App\Models\User;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class OfflineTransaction extends Model
{
    use UsesUuid;

    public function scopeUserId($query, $value)
    {
        return $query->where('user_id', $value);
    }

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
     * Save only if unique.
     *
     * @return boolean
     */
    public function saveIfUnique()
    {
        $oldTransaction = self::where('user_id', $this->user_id)
            ->where('bill_id', $this->bill_id)
            ->where('type', $this->type)
            ->where('amount', $this->amount)
            ->where('reference', $this->reference)
            ->where('transaction_source', $this->transaction_source)
            ->first();

        return $oldTransaction ? false : $this->save();
    }

    /**
     * Get user.
     *
     * @return Collection
     */
    public function scopeAmountByCycleDate($query, $cycleDate)
    {
        return $query->where('transaction_source', '!=', "transfer")
            ->whereDate('created_at', '<=', $cycleDate);
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

            logger([
                'type' => $trans->type,
                'user_balance' => $trans->user->balance,
                'amount' => $trans->amount,
                'description' => $trans->description,
            ]);

            $trans->balance = ($trans->type == 'debit') ? $trans->user->balance - $trans->amount : $trans->user->balance + $trans->amount;
        });
    }
}
