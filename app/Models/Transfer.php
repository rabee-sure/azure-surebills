<?php

namespace App\Models;

use Carbon\Carbon;
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
        
        'status',
        'transfer_fees',
        'net_amount',
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
     * Get the user's is Active.
     *
     * @param  string  $value
     * @return string
     */
    public function getDateFromToAttribute()
    {
        $from = Carbon::parse($this->filters['date']['from'])->toDateTimeString()?? '';
        $to = Carbon::parse($this->filters['date']['to'])->toDateTimeString()?? '';
        return  $from.' - '. $to;
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

    /**
     * Get bank.
     *
     * @return Collection
     */
    public function bank()
    {
        return $this->belongsTo(Bank::class);
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
