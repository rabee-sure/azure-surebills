<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductListResource;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Http\Requests\ProductApiRequest;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::owner($request->user->id)->get();
        $productsCollection = ProductListResource::collection($products);

        return $productsCollection;

    }

    public function show($id, Request $request)
    {
        $product = Product::with('images')->find($id);
        if($product->user_id == $request->user->id){
            return $product;
        }else{
            return response()->json(['authorization' => 'not authorized to show this product'], 403);
        }
    }
    
    public function store(ProductApiRequest $request){
        $name = ["en" => $request->name_en,"ar" => $request->name_ar];
        $discription = ["en" => $request->discription_en,"ar" => $request->discription_ar];
        
        $images = array();
        if(!empty($request->image) && count($request->image) > 0){
            foreach($request->image as $image){
                $file = $image;
                $file_name = time().'-'.$file->getClientOriginalName();
                $destinationPath = storage_path('/app/public/products');
                $file->move($destinationPath, $file_name);
                $images[]['image'] = $file_name;
            }
        }

        
        $product = Product::create([
            'name' => $name,
            'discription' => $discription,
            'price' => $request->price,
            'sort_number' => $request->sort_number,
            'active' => $request->active,
            'category_id' => $request->category_id,
            'user_id' => $request->user->id,
        ]);
        
        if(!empty($images)){
            $product->images()->createMany($images);
        }

        return $product;
    }

    public function update($id, ProductApiRequest $request){
        
        
        $product = Product::find($id);
        
        if($product->user_id == $request->user->id){
            $name = ["en" => $request->name_en,"ar" => $request->name_ar];
            $discription = ["en" => $request->discription_en,"ar" => $request->discription_ar];

            if(isset($request->image) && count($request->image) > 0){
                $images = array();
                foreach($request->image as $image){
                    $file = $image;
                    $file_name = time().'-'.$file->getClientOriginalName();
                    $destinationPath = storage_path('/app/public/products');
                    $file->move($destinationPath, $file_name);
                    $images[]['image'] = $file_name;
                }
            }
    
            $product->update([
                'name' => $name,
                'discription' => $discription,
                'price' => $request->price,
                'sort_number' => $request->sort_number,
                'active' => $request->active,
                'category_id' => $request->category_id,
            ]);
            
            if(isset($request->deletedImages) && !empty($request->deletedImages)){
                $product->images()->whereIn('id',$request->deletedImages)->delete();
            }
            if(isset($images) && !empty($images)){
                $product->images()->createMany($images);
            }
    
            return $product;
        }else{
            return response()->json(['authorization' => 'not authorized to updated this product'], 403);
        }
    }

    public function delete($id, Request $request){
        $product = Product::findOrFail($id);

        if($product->user_id == $request->user->id){
            $product->delete();

            return response()->json(['deleted_at' => $product->deleted_at], 200);
        }else{
            return response()->json(['authorization' => 'not authorized to delete this product'], 403);
        }
    }
}
