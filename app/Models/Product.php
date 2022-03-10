<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;


class Product extends Model
{
    use HasFactory;
    use HasTranslations;
    use SoftDeletes;

    public $translatable = ['name', 'discription'];

    protected $fillable = [ 
        'name', 
        'discription', 
        'price', 
        'sort_number',
        'active',
        'category_id'
    ];

    public function scopeActive($query)
    {
    	return $query->where('active', true);
    }

    public function scopeName($query, $keyword)
    {
    	return $query->where('name->en', 'like', '%'.$keyword.'%')->orWhere('name->ar', 'like', '%'.$keyword.'%');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id');
    }

    public function images()
    {
        return $this->hasMany('App\Models\ProductImage', 'product_id');
    }
}
