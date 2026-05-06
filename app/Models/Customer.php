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
        'other_buyer_id',
        'vat_registration_number'
    ];

    public function scopeUserId($query, $value)
    {
        return $query->where('user_id', $value);
    }

    public function scopeWalkinCustomer($query, $value)
    {
        return $query->where('walkin_customer', $value);
    }

    public function scopeName($query, $name)
    {
    	return $query->where('name', 'like', '%'.$name.'%');
    }

    public function scopeMobile($query, $mobile)
    {
    	return $query->where('mobile', 'like', '%'.$mobile.'%');
    }

    public function scopeOwner($query, $user_id)
    {
    	return $query->where('user_id', $user_id);
    }

    public function scopeMatchByMobileOrEmail($query, $userId, $mobile = null, $email = null)
    {
        return $query->where('user_id', $userId)
            ->where(function ($query) use ($mobile, $email) {
                $hasCondition = false;

                if (!is_null($mobile)) {
                    $query->where('mobile', $mobile);
                    $hasCondition = true;
                }

                if (!is_null($email)) {
                    if ($hasCondition) {
                        $query->orWhere('email', $email);
                    } else {
                        $query->where('email', $email);
                    }

                    $hasCondition = true;
                }

                if (!$hasCondition) {
                    $query->whereRaw('1 = 0');
                }
            });
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
