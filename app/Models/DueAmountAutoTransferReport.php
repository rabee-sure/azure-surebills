<?php

namespace App\Models;

use App\Traits\HasEncryptedAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DueAmountAutoTransferReport extends Model
{
    use HasFactory, HasEncryptedAttributes;

    public $table = 'due_amount_auto_transfer_report';
    protected $fillable = ['auto_transfer_id', 'merchant_id', 'merchant_name', 'merchant_iban', 'bank', 'total_amount' ,'total_fees' ,'total_fees_vat',
                            'total_refund', 'bank_charges', 'net_due', 'channel_id', 'reference'];

    /**
     * The attributes that should be encrypted.
     *
     * @var array
     */
    protected $encrypted = [
        'merchant_iban',
    ];
}
