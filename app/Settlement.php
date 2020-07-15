<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Settlement extends Model
{
    protected $fillable = [
    	'bills_paid_from',
		'bills_paid_to',
		'total_number_of_bills',
		'total_amount_of_bills',
		'total_paid_amount',
		'total_fees_amount',
		'user_id',
	];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'bills_paid_from' => 'date',
        'bills_paid_to' => 'date',
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

	/**
     * Get bills.
     *
     * @return Collection
     */
    public function bills()
    {
        return $this->hasMany(Bill::class);
    }
}
