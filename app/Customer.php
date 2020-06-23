<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['name', 'email', 'mobile', 'Customer', 'user_id'];

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
