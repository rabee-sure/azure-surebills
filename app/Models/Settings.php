<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Settings extends Model
{
    use HasTranslations;

    protected $table = "settings";

    public $translatable = ['header_bill', 'footer_bill'];

  	protected $fillable = [
        'user_id',
        'add_tax',
        'tax_value',
		'default_lang',
		'active_lang',
		'create_send_sms',
		'create_send_email',
		'paid_send_sms',
		'paid_send_email',
        'header_bill',
        'footer_bill'
    ];

    public function scopeUserId($query, $value)
    {
        return $query->where('user_id', $value);
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

}
