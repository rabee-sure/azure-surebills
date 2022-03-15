<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CategoryProductResource;
use App\Models\Category;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Customer;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Http\Requests\CustomerApiRequest;

class PosController extends Controller
{
    public function getActiveTopCategory(Request $request)
    {
        $categories = Category::active()->where('parent_id', 0)->get();
        $categoriesCollection = collect($categories);

        return $categoriesCollection;
    }

    public function getActiveSubCategoryProducts($category_id, Request $request)
    {
        $categories = Category::active()->where('parent_id', $category_id)->get();
        $categoriesCollection = CategoryProductResource::collection($categories);

        return $categoriesCollection;
    }

    public function getProduct($product_id, Request $request)
    {
        $product = Product::with('images')->find($product_id);
        
        return $product;
    }

    public function searchForProduct($keyword, Request $request)
    {
        $products = Product::name($keyword)->get();

        return $products;
    }

    public function searchForCustomer($name, Request $request)
    {
        $customers = Customer::name($name)->get();

        return $customers;
    }

    public function customerStore(CustomerApiRequest $request)
    {
        $application = $request->application;
        $user = $application->user ?? null;

        $customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'notes' => $request->notes,
            'user_id' => $user->id,

            'bullding_no' => $request->bullding_no,
            'street_name' => $request->street_name,
            'district' => $request->district,
            'city' => $request->city,
            'postal_code' => $request->postal_code,
            'additional_no' => $request->additional_no,
            'other_buyer_id' => $request->other_buyer_id,
            'vat_registration_number' => $request->vat_registration_number,
        ]);

        return $customer;
    }
}
