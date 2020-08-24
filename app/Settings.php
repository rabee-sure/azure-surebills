<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $table = "settings";

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
    ];
    
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
