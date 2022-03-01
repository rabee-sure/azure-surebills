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

    public function parent()
    {
       return $this->hasOne('App\Models\Category', 'parent_id','id');
    }
    public function childiren()
    {
       return $this->hasMany('App\Models\Category', 'parent_id')->with('childiren');
    }

    public function products()
    {
        return $this->hasMany('App\Models\Product', 'category_id');
    }
}
