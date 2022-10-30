<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class ProductCustomization extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    public $translatable = ['name'];
    protected $fillable = [ 'name', 'price', 'product_id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
