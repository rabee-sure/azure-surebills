<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfflinePaymentLog extends Model
{
    protected $fillable = [
        'id',
        'tx_rslt',
        'results',
        'bill_id',
        'payment_method',
        'tid',
        'bank',
        'amount'
    ];

    protected $casts = [
        'results'   =>  'array'
    ];

    /**
     * Get bill.
     *
     * @return Collection
     */
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}
