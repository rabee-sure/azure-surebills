<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchantSummaryAutoTransferReport extends Model
{
    use HasFactory;
    public $table = 'merchant_summary_auto_transfer_report';
    protected $fillable = ['auto_transfer_id', 'client_id', 'payment_type', 'no_of_trx', 'total_amount', 'total_fees', 'total_fees_vat',
                            'total_fees_variable_rate', 'total_fees_fixed_rate', 'sure_variable_rate', 'sure_fixed_rate', 'channel_variable_rate',
                            'channel_fixed_rate', 'sure_fees', 'sure_vat', 'channel_fees', 'channels_vat', 'channel_id'];
}
