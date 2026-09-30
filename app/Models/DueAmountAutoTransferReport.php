<?php

namespace App\Models;

use App\Traits\HasEncryptedAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class DueAmountAutoTransferReport extends Model
{
    use HasFactory, HasEncryptedAttributes, HasTranslations {
        HasTranslations::setAttribute insteadof HasEncryptedAttributes;
        HasTranslations::setAttribute as setTranslatableAttribute;
        HasTranslations::getAttributeValue as getTranslatableAttributeValue;
    }

    public $table = 'due_amount_auto_transfer_report';

    public $translatable = ['bank'];

    protected $fillable = ['auto_transfer_id', 'merchant_id', 'merchant_name', 'merchant_iban', 'bank', 'total_amount', 'total_fees', 'total_fees_vat',
        'total_refund', 'bank_charges', 'net_due', 'channel_id', 'reference'];

    /**
     * The attributes that should be encrypted.
     *
     * @var array
     */
    protected $encrypted = [
        'merchant_iban',
    ];

    public function setAttribute($key, $value)
    {
        if ($this->shouldEncryptAttribute($key) && ! empty($value) && ! $this->isEncrypted($value)) {
            $value = encrypt($value);
        }

        return $this->setTranslatableAttribute($key, $value);
    }

    public function getAttributeValue($key): mixed
    {
        if ($key === 'bank' && ! self::bankValueIsTranslationSnapshot($this->getAttributeFromArray('bank'))) {
            return $this->getAttributeFromArray('bank');
        }

        return $this->getTranslatableAttributeValue($key);
    }

    public static function bankValueIsTranslationSnapshot(mixed $raw): bool
    {
        if (! is_string($raw) || $raw === '') {
            return false;
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) && ! array_is_list($decoded);
    }
}
