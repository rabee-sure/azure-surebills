<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    
    protected $table = 'settlements';

    protected $fillable = [
		'amount',
		'user_id',
        'created_by_id',
        'note',
        'attachment',

        //bank_id info
        'bank_id',
        'iban_number',
        'beneficiary_name',
        'filters',
	];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'filters' => 'array',
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
        return $this->belongsToMany(Bill::class);
    }

    /**
     * Get user.
     *
     * @return Collection
     */
    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
