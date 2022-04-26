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
        'parent_id',
        'user_id'
    ];

    public function scopeActive($query)
    {
    	return $query->where('active', true);
    }

    public function scopeOwner($query, $user_id)
    {
    	return $query->where('user_id', $user_id);
    }

    public function parent()
    {
       return $this->belongsTo('App\Models\Category', 'parent_id','id');
    }
    public function childiren()
    {
       return $this->hasMany('App\Models\Category', 'parent_id')->with('childiren');
    }

    public function child(){
        return $this->hasMany('App\Models\Category', 'parent_id', 'id');
    }

    public function products()
    {
        return $this->hasMany('App\Models\Product', 'category_id');
    }

    public function deleteDependency(){
        $array_of_ids = self::getChildren($this);
        array_push($array_of_ids, $this->id);

        $this->whereIn('id', $array_of_ids)->delete();

        $product = new \App\Models\Product;
        $product->whereIn('category_id', $array_of_ids)->delete();
    }

    public function deleteMove($newCat)
    {
        $category = new \App\Models\Category;
        $product = new \App\Models\Product;

        $category->where('parent_id', $this->id)->update(['parent_id' => $newCat]);
        $product->where('category_id', $this->id)->update(['category_id' => $newCat]);

        $this->delete();
    }

    private function getChildren($category){
        $ids = [];
        foreach ($category->child as $cat) {
            $ids[] = $cat->id;
            $ids = array_merge($ids, self::getChildren($cat));
        }
        return $ids;
    }
}
