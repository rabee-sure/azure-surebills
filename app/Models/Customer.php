<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['name', 'email', 'mobile', 'Customer', 'user_id', 'notes',
        'bullding_no',
        'street_name',
        'district',
        'city',
        'postal_code',
        'additional_no',
        'other_buyer_id'
    ];

    /**
     * Get bills.
     *
     * @return Collection
     */
    public function bills()
    {
    	return $this->hasMany(Bill::class);
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
