<?php

namespace App\Models;

use App\Traits\HasEncryptedAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZatcaInvoice extends Model
{
    use HasFactory, HasEncryptedAttributes;

    protected $fillable = [
        'uuid',
        'number',
        'status',
        'bill_type',
        'refrence_bill_id',
        'merchant_id',
        'merchant_name',
        'merchant_email',
        'merchant_vat_registration_number',
        'merchant_crn',
        'merchant_tin',
        'merchant_building_no',
        'merchant_street_name',
        'merchant_district',
        'merchant_city',
        'merchant_postal_code',
        'customer_name',
        'customer_vat_registration_number',
        'customer_building_no',
        'customer_street_name',
        'customer_district',
        'customer_city',
        'customer_postal_code',
        'bill_amount',
        'tax_value',
        'vat',
        'discount',
        'total',
        'invoice_date',
        'paid_at',
    ];

    /**
     * The attributes that should be encrypted.
     *
     * @var array
     */
    protected $encrypted = [
        'merchant_crn'    // Commercial Registration Number
    ];

    public function merchant(){
        return $this->belongsTo(ZatcaMerchant::class, 'merchant_id', 'id');
    }

    public function refranceBill(){
        return $this->belongsTo(ZatcaInvoice::class, 'refrence_bill_id', 'id');
    }

    public function items(){
        return $this->hasMany(ZatcaInvoiceItem::class, 'bill_id', 'id');
    }
}
