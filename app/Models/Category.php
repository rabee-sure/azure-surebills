<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasTranslations;
    use SoftDeletes;

    public $translatable = ['name'];

    protected $fillable = [ 
        'name', 
        'image', 
        'sort_number',
        'active',
        'parent_id'
    ];

    public function scopeActive($query)
    {
    	return $query->where('active', true);
    }

    public function subcategory()
    {
        return $this->hasMany('\App\Models\Category', 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo('\App\Models\Category', 'parent_id');
    }
}
