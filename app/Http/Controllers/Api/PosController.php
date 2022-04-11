<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\SubCategoryResource;
use App\Http\Resources\CategoryPosListResource;
use App\Http\Resources\BillApiResource;

use App\Models\Category;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Customer;
use App\Models\ProductImage;
use App\Models\PosOrder;
use App\Models\PosOrderItem;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException as ValidationsException;

use Illuminate\Http\Request;
use App\Http\Requests\CustomerApiRequest;
use App\Http\Requests\PosOrderApiRequest;

use App\Events\BillCreated;

class PosController extends Controller
{
    public function getActiveTopCategory(Request $request)
    {
        $categories = Category::active()->owner(auth('api')->user()->id)->where('parent_id', 0)->orderBy('sort_number')->get();
        $categoriesCollection = CategoryPosListResource::collection($categories);

        return $categoriesCollection;
    }

    public function getActiveSubCategory($category_id, Request $request)
    {
        $categories = Category::active()->owner(auth('api')->user()->id)->where('parent_id', $category_id)->orderBy('sort_number')->get();
        $categoriesCollection = CategoryPosListResource::collection($categories);

        return $categoriesCollection;
    }

    public function getActiveCategoryProducts($category_id, Request $request)
    {
        $products = Product::active()->owner(auth('api')->user()->id)->where('category_id', $category_id)->orderBy('sort_number')->get();
        $productsCollection = ProductResource::collection($products);

        return $productsCollection;
    }

    public function getActiveProducts(Request $request)
    {
        $products = Product::active()->owner(auth('api')->user()->id)->orderBy('sort_number')->get();
        $productsCollection = ProductResource::collection($products);

        return $productsCollection;
    }

    public function getProduct($product_id, Request $request)
    {
        $product = Product::where('id', $product_id)->get();
        if($product->isEmpty()){
            return response()->json(['message' => 'not found'], 404);
        }else{
            if($product[0]->user_id == auth('api')->user()->id){
                $productCollection = ProductResource::collection($product);
                return $productCollection;
            }else{
                return response()->json(['authorization' => 'not authorized to show this product'], 403);
            }
        }
    }

    public function searchForProduct($keyword, Request $request)
    {
        $products = Product::name($keyword)->owner(auth('api')->user()->id)->get();

        return $products;
    }

    public function searchForCustomer($mobile, Request $request)
    {
        $customers = Customer::mobile($mobile)->owner(auth('api')->user()->id)->get();

        return $customers;
    }

    public function customerStore(CustomerApiRequest $request)
    {
        $customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'notes' => $request->notes,
            'user_id' => auth('api')->user()->id,

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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function orderStore(PosOrderApiRequest $request)
    {
        $user = auth('api')->user();
        
        $order = PosOrder::create([
            'user_id' => $user->id,
            'business_name' => $user->business_name,
            
            'customer_id' => $request->customer_id,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_mobile' => $request->customer_mobile,
            'customer_notes' => $request->customer_notes,
            'bullding_no' => $request->customer_bullding_no,
            'street_name' => $request->customer_street_name,
            'district' => $request->customer_district,
            'city' => $request->customer_city,
            'postal_code' => $request->customer_postal_code,
            'additional_no' => $request->customer_additional_no,
            'other_buyer_id' => $request->customer_other_buyer_id,
            'vat_registration_number' => $request->customer_vat_registration_number,

            'add_discount' => $request->add_discount ?? false,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,

            'add_tax' => $request->add_tax ?? false,
            'tax_name' => $request->tax_name,
            'tax_value' => $request->tax_value,
        ]);


        foreach ($request->items as $item) {
            PosOrderItem::create([
                'order_id' => $order->id,
                'product_name' => $item['name'],
                'product_category' => $item['category'],
                'product_price' => $item['price'],
                'quantity' => $item['quantity'],
                'total' => $item['quantity']*$item['price'],
            ]);
        }

        $sub_total = $order->items->sum('total');
        $discount = 0;
        $vat = 0;
        if($request->add_discount){
            switch ($request->discount_type) {
                case 'fixed':
                    $discount = $request->discount_value;
                    break;
                case 'percentage':
                    $discount = $sub_total * $request->discount_value / 100;
                    break;
            }
        }

        if($request->add_tax !== null){
            $order->add_tax = $request->add_tax;
            $order->tax_value = $request->tax_value;
            $vat = ($sub_total -$discount) * $request->tax_value /100;

        }elseif($user->settings->add_tax){
            $order->add_tax = $user->settings->add_tax;
            $order->tax_value = $user->settings->tax_value;
            $vat = ($sub_total -$discount) * $user->settings->tax_value /100;
        }

        $order->discount = $discount;
        $order->vat = $vat;
        $order->number = $order->getNumber();
        $order->sub_total = $sub_total;
        $order->total = $sub_total - $discount + $vat;
        $order->save();

        
        $bill = DB::transaction(function () use ($order) {
            $user = User::find($order->user_id);

            $bill = Bill::create([
                'user_id' => $user->store_main_user_id ?? $user->id,
                'status' => 'pending',
                'business_name' => $order->business_name,
                'customer_id' => $order->customer_id,
                'customer_name' => $order->customer_name,
                'customer_email' => $order->customer_email,
                'customer_mobile' => $order->customer_mobile,
                'customer_notes' => $order->customer_notes,

                'reference_id' => $order->number,

                'expiry_date' => 30,
                'expiry_hours' => $request->expiry_hours ?? 0,
                'expiry_minutes' => $request->expiry_minutes ?? 0,
                'due_date' => date('Y-m-d', strtotime(str_replace('/', '-', $order->created_at))),

                'add_discount' => $order->add_discount  ? "on" : 0,
                'discount_type' => $order->add_discount  ? $order->discount_type : false,
                'discount_value' => $order->add_discount  ? $order->discount_value : null,

                'add_tax' => $order->add_tax ? "on" : false,
                'tax_name' => $order->add_tax ? $order->tax_name : null,
                'tax_value' => $order->add_tax ? $order->tax_value : null,

                'send_sms' => false,
                'send_email' => false,
            ]);

            foreach ($order->items as $item) {
                BillItem::create([
                    'bill_id' => $bill->id,
                    'product_name' => $item->product_name,
                    'product_price' => $item->product_price,
                    'quantity' => $item->quantity,
                    'total' => $item->quantity * $item->product_price,
                ]);
            }

            $sub_total = $bill->items->sum('total');
            $discount = 0;
            $vat = 0;
            $payment_fees = 0;

            if ($user->pay_fees == "client") {
                $payment_fees = ($sub_total * ($user->credit_cards_percentage / 100)) + $user->credit_cards_fixed;
            }

            if ($order->add_discount) {
                switch ($order->discount_type) {
                    case 'fixed':
                        $discount = $order->discount_value;
                        break;
                    case 'percentage':
                        $discount = ($sub_total + $payment_fees) * $order->discount_value / 100;
                        break;
                }
            }

            if ($order->add_tax) {
                $vat = ($sub_total + $payment_fees - $discount) * $order->tax_value / 100;
            }

            $bill->payment_fees = $payment_fees;
            $bill->discount = $discount;
            $bill->vat = $vat;
            $bill->number = $bill->getNumber();
            $bill->sub_total = $sub_total;
            $bill->total = $sub_total + $payment_fees - $discount + $vat;
            if ($bill->total <= 0) {
                throw ValidationsException::withMessages(['total' => __('The total must be greater than 0')]);
            }
            $bill->save();
            return $bill;
        });

        event(new BillCreated($bill));

        return new BillApiResource($bill);
    }
}