<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategoryListResource;
use App\Http\Resources\CategorySingleResource;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\CategoryApiRequest;

class CategoryController extends Controller
{

    public function index(Request $request)
    {
        $categories = Category::get();
        $categoriesCollection = CategoryListResource::collection($categories);

        return $categoriesCollection;

    }

    public function topCategories(Request $request)
    {
        $categories = Category::where('parent_id', 0)->withTrashed()->get();
        $categoriesCollection = CategoryResource::collection($categories);

        return $categoriesCollection;

    }

    public function subCategories($parent_id, Request $request)
    {
        $category = Category::find($parent_id);
        return CategoryResource::collection($category->childiren);
    }

    public function show($category_id, Request $request)
    {
        $category = Category::find($category_id);
        return $category;
    }
    
    public function store(CategoryApiRequest $request){
        $name = ["en" => $request->name_en,"ar" => $request->name_ar];

        $parent = ($request->parent_id) ? $request->parent_id : 0;

        if ($request->hasFile('image')) {
	        $file = $request->file('image');
	        $file_name = time().'-'.$file->getClientOriginalName();
	        $destinationPath = storage_path('/app/public/categories');
	        $file->move($destinationPath, $file_name);
            $image = $file_name;
	    }

        $category = Category::create([
            'name' => $name,
            'sort_number' => $request->sort_number,
            'active' => $request->active,
            'parent_id' => $parent,
            'image' => $image,
        ]);
        
        return $category;
    }

    public function update($id, CategoryApiRequest $request){
        
        $name = ["en" => $request->name_en,"ar" => $request->name_ar];

        $parent = ($request->parent_id) ? $request->parent_id : 0;

        $category = Category::find($id);

        if ($request->hasFile('image')) {
	        $file = $request->file('image');
	        $file_name = time().'-'.$file->getClientOriginalName();
	        $destinationPath = storage_path('/app/public/categories');
	        $file->move($destinationPath, $file_name);
            $image = $file_name;
	    }else{
            $image = $category->image;
        }

        $category = Category::find($id);

        $category->update([
            'name' => $name,
            'sort_number' => $request->sort_number,
            'active' => $request->active,
            'parent_id' => $parent,
            'image' => $image,
        ]);
        
        return $category;
    }

    public function delete($id){
        $category = Category::findOrFail($id);

        $category->delete();

        return response()->json(['deleted_at' => $category->deleted_at], 200);
    }
}
