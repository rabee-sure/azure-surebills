<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Http\Requests\ProductApiRequest;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::withTrashed()->get();
        $productsCollection = ProductResource::collection($products);

        return $productsCollection;

    }

    public function show($id, Request $request)
    {
        $product = Product::with('images')->find($id);
        
        return $product;
    }
    
    public function store(ProductApiRequest $request){
        $name = ["en" => $request->name_en,"ar" => $request->name_ar];
        $discription = ["en" => $request->discription_en,"ar" => $request->discription_ar];
        
        if(count($request->image) > 0){
            $images = array();
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
        ]);
        
        $product->images()->createMany($images);

        return $product;
    }

    public function update($id, ProductApiRequest $request){
        
        $name = ["en" => $request->name_en,"ar" => $request->name_ar];
        $discription = ["en" => $request->discription_en,"ar" => $request->discription_ar];

        if(count($request->image) > 0){
            $images = array();
            foreach($request->image as $image){
                $file = $image;
                $file_name = time().'-'.$file->getClientOriginalName();
                $destinationPath = storage_path('/app/public/products');
                $file->move($destinationPath, $file_name);
                $images[]['image'] = $file_name;
            }
        }

        $product = Product::find($id);

        $product->update([
            'name' => $name,
            'discription' => $discription,
            'price' => $request->price,
            'sort_number' => $request->sort_number,
            'active' => $request->active,
            'category_id' => $request->category_id,
        ]);
        
        $product->images()->whereIn('id',$request->deletedImages)->delete();
        $product->images()->createMany($images);

        return $product;
    }

    public function delete($id){
        $product = Product::findOrFail($id);

        $product->delete();

        return response()->json(['deleted_at' => $product->deleted_at], 200);
    }
}
