<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZatcaMerchant extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'email',
        'business_name_en',
        'vat_registration_number',
        'tin',
        'crn',
        'invoices_type',
        'business_category',
        'building_no',
        'street_name',
        'district',
        'city',
        'postal_code',
        'additional_number',
        'other_buyer_id',
        'otp',
        'zatca_pih',
        'complianceCertificate',
        'complianceSecret',
        'complianceRequestID',
        'productionCertificate',
        'productionCertificateSecret',
        'productionCertificateRequestID',
        'privateKey',
        'publicKey',
        'csrKey',
        'configData',
    ];
}
