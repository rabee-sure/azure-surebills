<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZatcaLog extends Model
{
    protected $fillable = [
        'id',
        'uuid',
        'payload',
        'api',
        'response',
        'response_code',
        'reporting_status',
        'clearance_status',
        'disposition_message',
        'status',
        'qrSellert_status',
        'qrBuyert_status',
        'parentable_id',
        'parentable_type',
    ];

    protected $casts = [
        'payload'   =>  'array',
        'response'   =>  'array'
    ];

    /**
     * Get bill.
     *
     * @return Collection
     */
    public function parentable() {
        return $this->morphTo();
    }
}
