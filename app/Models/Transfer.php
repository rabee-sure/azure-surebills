<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Transfer extends Model implements HasMedia
{
    use InteractsWithMedia;

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

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('transfers_transactions')->singleFile();
    }

    /**
     * Get the user's is Active.
     *
     * @param  string  $value
     * @return string
     */
    public function getDateFromToAttribute()
    {
        $to = (isset($this->filters['date']['to'])) ? Carbon::parse($this->filters['date']['to'])->toDateString(): null;        
        $cycle_date = (isset($this->filters['date']['cycle_date'])) ? Carbon::parse($this->filters['date']['cycle_date'])->toDateString(): '';
        return  $to ?? $cycle_date;
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
     * Get bills.
     *
     * @return Collection
     */
    public function transactions()
    {
        return $this->belongsToMany(Transaction::class)
            ->orderBy('created_at', 'ASC')
            ->orderBy('order', 'ASC')
            ->orderBy('receipt', 'ASC');
    }
    
    /**
     * Pending
     */
    public function scopePending($query){
        $query->where('status', 'pending')
            ->orWhere(function($q){
                $q->where('status', 'completed')->whereNull('attachment');
            });
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


    /**
     * Get user.
     *
     * @return Collection
     */
    public function logs()
    {
        return $this->hasMany(TransferLog::class);
    }

}