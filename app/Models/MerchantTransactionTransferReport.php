<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchantTransactionTransferReport extends Model
{
    use HasFactory;
    public $table = 'merchant_transaction_transfer_report';
    protected $fillable = [
        'created_at',
        'description',
        'type',
        'amount',
        'transaction_id',
        'bill_id',
        'bill_reference_id',
        'bill_number',
        'bill_user_id',
        'bill_business_name',
        'card_brand',
        'card',
        'source',
        'bill_application_channel_id',
        'bill_application_channel_name',
        'report_type'
    ];

    public $timestamps = false;
}
