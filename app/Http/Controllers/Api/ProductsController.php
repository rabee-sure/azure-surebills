<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductListResource;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Http\Requests\ProductApiRequest;
use App\Models\ProductCustomization;
use App\Services\GetAuthUser;
use Exception;
use Illuminate\Support\Facades\DB;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $authUser = GetAuthUser::authUser($request);

        $owner_id = ($authUser->store_main_user_id != null) ? $authUser->store_main_user_id : $authUser->id;

        $products = Product::owner($owner_id)->get();
        $productsCollection = ProductListResource::collection($products);

        return $productsCollection;

    }

    public function show($id, Request $request)
    {
        $authUser = GetAuthUser::authUser($request);

        $owner_id = ($authUser->store_main_user_id != null) ? $authUser->store_main_user_id : $authUser->id;

        $product = Product::with('images', 'customizations')->find($id);
        if($product->user_id == $owner_id){
            return $product;
        }else{
            return response()->json(['authorization' => 'not authorized to show this product'], 403);
        }
    }

    public function store(ProductApiRequest $request){
        $authUser = GetAuthUser::authUser($request);

        $owner_id = ($authUser->store_main_user_id != null) ? $authUser->store_main_user_id : $authUser->id;

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

        DB::beginTransaction();
        try {
            DB::commit();
            $product = Product::create([
                'name' => $name,
                'discription' => $discription,
                'price' => $request->price,
                'sort_number' => $request->sort_number,
                'active' => $request->active,
                'category_id' => $request->category_id,
                'user_id' => $owner_id,
                'enable_customizations' => $request->enable_customizations,
            ]);

            if(!empty($images)){
                $product->images()->createMany($images);
            }

            $this->storeProductCustomizations($request, $product);
            return $product;

        } catch (Exception $e) {
            // dd($e->getMessage());
            DB::rollback();
        }
    }

    private function storeProductCustomizations($request, $product)
    {
        if($request->enable_customizations)
        {
            if(count($product->customizations) > 0)
            {
                $product->customizations()->whereNotIn('id', $request->customization_id)->delete();
            }

            for($i=0; $i<count($request->customization_name_ar); $i++)
            {
                $name = ["en" => $request->customization_name_en[$i],"ar" => $request->customization_name_ar[$i]];
                if($request->has('customization_id'))
                {
                    ProductCustomization::updateOrCreate(['id' => $request->customization_id[$i]], ['product_id' => $product->id, 'name' => $name, 'price' => $request->customization_price[$i]]);
                }
                else
                {
                    ProductCustomization::create(['product_id' => $product->id, 'name' => $name, 'price' => $request->customization_price[$i]]);
                }
            }
        }
    }

    public function update($id, ProductApiRequest $request){
        $authUser = GetAuthUser::authUser($request);

        $owner_id = ($authUser->store_main_user_id != null) ? $authUser->store_main_user_id : $authUser->id;

        $product = Product::find($id);

        if($product->user_id == $owner_id){
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
                'enable_customizations' => $request->enable_customizations,
            ]);

            if(isset($request->deletedImages) && !empty($request->deletedImages)){
                $product->images()->whereIn('id',$request->deletedImages)->delete();
            }
            if(isset($images) && !empty($images)){
                $product->images()->createMany($images);
            }

            $this->storeProductCustomizations($request, $product);
            return $product;
        }else{
            return response()->json(['authorization' => 'not authorized to updated this product'], 403);
        }
    }

    public function delete($id, Request $request){
        $authUser = GetAuthUser::authUser($request);

        $owner_id = ($authUser->store_main_user_id != null) ? $authUser->store_main_user_id : $authUser->id;

        $product = Product::findOrFail($id);

        if($product->user_id == $owner_id){
            $product->delete();

            return response()->json(['deleted_at' => $product->deleted_at], 200);
        }else{
            return response()->json(['authorization' => 'not authorized to delete this product'], 403);
        }
    }
}
