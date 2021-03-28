<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Bank extends Model
{
    use HasTranslations;

    public $translatable = ['name'];

    protected $fillable = [
        'code', 
        'active', 
        'sort_number', 
        'fees'
    ];

    public function scopeActive($query)
    {
    	return $query->where('active', true);
    }


    /**
     * Get users.
     *
     * @return Collection
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
