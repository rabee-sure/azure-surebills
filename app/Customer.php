<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['name', 'email', 'mobile', 'Customer'];

    public function bills()
    {
    	return $this->hasMany(Bill::class);
    }
}
