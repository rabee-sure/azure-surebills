<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategoryListResource;
use App\Http\Resources\CategoryOptionsResource;
use App\Http\Resources\CategorySingleResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\CategoryApiRequest;
use App\Services\GetAuthUser;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $authUser = GetAuthUser::authUser($request);
        $owner_id = ($authUser->store_main_user_id != null) ? $authUser->store_main_user_id : $authUser->id;
        
        $categories = Category::owner($owner_id)->get();
        $categoriesCollection = CategoryListResource::collection($categories);

        return $categoriesCollection;

    }

    public function getAll(Request $request)
    {
        $authUser = GetAuthUser::authUser($request);

        $owner_id = ($authUser->store_main_user_id != null) ? $authUser->store_main_user_id : $authUser->id;

        $categories = Category::owner($owner_id)->get();
        $categoriesCollection = CategoryOptionsResource::collection($categories);

        return response()->json($categoriesCollection, 200);

    }

    public function topCategories(Request $request)
    {
        $authUser = GetAuthUser::authUser($request);

        $owner_id = ($authUser->store_main_user_id != null) ? $authUser->store_main_user_id : $authUser->id;

        $categories = Category::owner($owner_id)->where('parent_id', 0)->get();
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
        $authUser = GetAuthUser::authUser($request);

        $owner_id = ($authUser->store_main_user_id != null) ? $authUser->store_main_user_id : $authUser->id;

        $category = Category::find($category_id);
        if($category->user_id == $owner_id){
            return $category;
        }else{
            return response()->json(['authorization' => 'not authorized to show this category'], 403);
        }
    }
    
    public function store(CategoryApiRequest $request){
        $authUser = GetAuthUser::authUser($request);

        $owner_id = ($authUser->store_main_user_id != null) ? $authUser->store_main_user_id : $authUser->id;

        $name = ["en" => $request->name_en,"ar" => $request->name_ar];

        $parent = ($request->parent_id) ? $request->parent_id : 0;

        $image = null;
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
            'user_id' => $owner_id,
        ]);
        
        return $category;
    }

    public function update($id, CategoryApiRequest $request){
        $authUser = GetAuthUser::authUser($request);
        
        $owner_id = ($authUser->store_main_user_id != null) ? $authUser->store_main_user_id : $authUser->id;
        
        $name = ["en" => $request->name_en,"ar" => $request->name_ar];

        $parent = ($request->parent_id) ? $request->parent_id : 0;

        $category = Category::find($id);

        if($category->user_id == $owner_id){

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
        }else{
            return response()->json(['authorization' => 'not authorized to updated this category'], 403);
        }
    }

    public function delete($id, Request $request){
        $authUser = GetAuthUser::authUser($request);
        
        $owner_id = ($authUser->store_main_user_id != null) ? $authUser->store_main_user_id : $authUser->id;

        $category = Category::findOrFail($id);

        if($category->user_id == $owner_id){
            $category->delete();
    
            return response()->json(['deleted_at' => $category->deleted_at], 200);
        }else{
            return response()->json(['authorization' => 'not authorized to delete this category'], 403);
        }
    }

    public function deleteDependency($id, Request $request){
        $authUser = GetAuthUser::authUser($request);
        
        $owner_id = ($authUser->store_main_user_id != null) ? $authUser->store_main_user_id : $authUser->id;

        $parent = Category::findOrFail($id);
        
        if($parent->user_id == $owner_id){
            $parent->deleteDependency();
    
            return response()->json(['deleted_at' => $parent->deleted_at], 200);
        }else{
            return response()->json(['authorization' => 'not authorized to delete this category'], 403);
        }
    }

    public function deleteMove(Request $request){
        $selectedCat = str_replace("'","",$request->selectedId);
        $selectedId = (int) $selectedCat;

        $authUser = GetAuthUser::authUser($request);
        
        $owner_id = ($authUser->store_main_user_id != null) ? $authUser->store_main_user_id : $authUser->id;

        $parent = Category::findOrFail($request->deletedId);
        
        if($parent->user_id == $owner_id){
            $parent->deleteMove($selectedId);
    
            return response()->json(['deleted_at' => $parent->deleted_at], 200);
        }else{
            return response()->json(['authorization' => 'not authorized to delete this category'], 403);
        }
    }

    public function childsCount($id, Request $request)
    {
        $authUser = GetAuthUser::authUser($request);
        
        $owner_id = ($authUser->store_main_user_id != null) ? $authUser->store_main_user_id : $authUser->id;

        $category = Category::findOrFail($id);

        if($category->user_id == $owner_id){
            return $category->childiren->count();
        }else{
            return response()->json(['authorization' => 'not authorized to delete this category'], 403);
        }
    }

    public function productsCount($id, Request $request)
    {
        $authUser = GetAuthUser::authUser($request);
        
        $owner_id = ($authUser->store_main_user_id != null) ? $authUser->store_main_user_id : $authUser->id;

        $category = Category::findOrFail($id);

        if($category->user_id == $owner_id){
            return $category->products->count();
        }else{
            return response()->json(['authorization' => 'not authorized to delete this category'], 403);
        }
    }
}
