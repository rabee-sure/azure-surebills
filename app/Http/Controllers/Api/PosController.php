<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryPosListResource;
use App\Http\Resources\BillPosApiResource;
use App\Http\Resources\OrdersBillsPosApiResource;
use App\Http\Resources\OrderBillPosApiResource;

use App\Models\Category;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Customer;
use App\Models\PosOrder;
use App\Models\PosOrderItem;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\User;
use App\Models\PosUserSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException as ValidationsException;

use Illuminate\Http\Request;
use App\Http\Requests\CustomerApiRequest;
use App\Http\Requests\PosOrderApiRequest;

use App\Events\BillCreated;
use App\Events\PosBillPaid;
use App\Events\PosSendBill;
use App\Http\Requests\PosRedirectToBillsProductsRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PosController extends Controller
{
    public function getAllActiveCategoryAndProducts(){
        $authUser = auth('api')->user();
        $owner_id = ($authUser->store_main_user_id != null) ? $authUser->store_main_user_id : $authUser->id;

        $categories = Category::active()->owner($owner_id)->orderBy('sort_number')->get();
        $categoriesCollection = CategoryPosListResource::collection($categories);

        $products = Product::active()->owner($owner_id)->orderBy('sort_number')->get();
        $productsCollection = ProductResource::collection($products);

        $collectionData = array();
        foreach($categoriesCollection as $category){
            $productsArr = $productsCollection->where('category_id', $category->id);
            $subcategories = $categoriesCollection->where('parent_id', $category->id);

            $collectionData[$category->id]['type'] = "category";
            $collectionData[$category->id]['name'] = array(
                'en' => $category->getTranslation('name', 'en'),
                'ar' => $category->getTranslation('name', 'ar'),
            );
            $collectionData[$category->id]['image'] = url('/').''.Storage::url('categories/').''.$category->image;
            $collectionData[$category->id]['sort_number'] = $category->sort_number;
            $collectionData[$category->id]['active'] = $category->active;
            $collectionData[$category->id]['parent_id'] = $category->parent_id;
            $collectionData[$category->id]['created_at'] = $category->created_at;
            $collectionData[$category->id]['updated_at'] = $category->updated_at;
            $collectionData[$category->id]['deleted_at'] = $category->deleted_at;

            $collectionData[$category->id]['products'] = [];
            $collectionData[$category->id]['subcategories'] = [];

            foreach ($productsArr as $productItem) {
                array_push($collectionData[$category->id]['products'], $productItem);
            }

            foreach($subcategories as $categoryItem){
                array_push($collectionData[$category->id]['subcategories'], $categoryItem);
            }
        }

        return $collectionData;
    }

    public function getActiveTopCategory()
    {
        $authUser = auth('api')->user();
        $owner_id = ($authUser->store_main_user_id != null) ? $authUser->store_main_user_id : $authUser->id;

        $categories = Category::active()->owner($owner_id)->where('parent_id', 0)->orderBy('sort_number')->get();
        $categoriesCollection = CategoryPosListResource::collection($categories);

        return $categoriesCollection;
    }

    public function getActiveSubCategory($category_id)
    {
        $authUser = auth('api')->user();
        $owner_id = ($authUser->store_main_user_id != null) ? $authUser->store_main_user_id : $authUser->id;

        $categories = Category::active()->owner($owner_id)->where('parent_id', $category_id)->orderBy('sort_number')->get();
        $categoriesCollection = CategoryPosListResource::collection($categories);

        return $categoriesCollection;
    }

    public function getActiveCategoryProducts($category_id)
    {
        $authUser = auth('api')->user();
        $owner_id = ($authUser->store_main_user_id != null) ? $authUser->store_main_user_id : $authUser->id;

        $products = Product::active()->owner($owner_id)->where('category_id', $category_id)->orderBy('sort_number')->get();
        $productsCollection = ProductResource::collection($products);

        return $productsCollection;
    }

    public function getActiveProducts()
    {
        $authUser = auth('api')->user();
        $owner_id = ($authUser->store_main_user_id != null) ? $authUser->store_main_user_id : $authUser->id;

        $products = Product::active()->owner($owner_id)->orderBy('sort_number')->get();
        $productsCollection = ProductResource::collection($products);

        return $productsCollection;
    }

    public function getProduct($product_id)
    {
        $authUser = auth('api')->user();
        $owner_id = ($authUser->store_main_user_id != null) ? $authUser->store_main_user_id : $authUser->id;

        $product = Product::where('id', $product_id)->get();
        if($product->isEmpty()){
            return response()->json(['message' => 'not found'], 404);
        }else{
            if($product[0]->user_id == $owner_id){
                $productCollection = ProductResource::collection($product);
                $firstItem = $productCollection->first();
                return $firstItem;
            }else{
                return response()->json(['authorization' => 'not authorized to show this product'], 403);
            }
        }
    }

    public function searchForProduct($keyword)
    {
        $authUser = auth('api')->user();
        $owner_id = ($authUser->store_main_user_id != null) ? $authUser->store_main_user_id : $authUser->id;

        $products = Product::name($keyword)->owner($owner_id)->get();

        return $products;
    }

    public function searchForCustomer($mobile)
    {
        $authUser = auth('api')->user();
        $owner_id = ($authUser->store_main_user_id != null) ? $authUser->store_main_user_id : $authUser->id;

        $customers = Customer::mobile($mobile)->owner($owner_id)->get();

        return $customers;
    }

    public function customerStore(CustomerApiRequest $request)
    {
        $authUser = auth('api')->user();
        $owner_id = ($authUser->store_main_user_id != null) ? $authUser->store_main_user_id : $authUser->id;

        $customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'notes' => $request->notes,
            'user_id' => $owner_id,

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
        $authUser = auth('api')->user();
        if($authUser->store_main_user_id != null){
            $user = $authUser->mainStoreUser;
        }else{
            $user = $authUser;
        }

        $customer = null;
        
        if($request->walkin_customer == 1){
            $customer = Customer::where('walkin_customer', 1)->owner($user->id)->first();
            if($customer == null){
                $customer = Customer::create([
                    'name' => 'FakeName',
                    'email' => null,
                    'mobile' => '555555555',
                    'notes' => null,
                    'user_id' => $user->id,

                    'bullding_no' => null,
                    'street_name' => null,
                    'district' => null,
                    'city' => null,
                    'postal_code' => null,
                    'additional_no' => null,
                    'other_buyer_id' => null,
                    'vat_registration_number' => null,
                ]);

                $customer->walkin_customer = 1;
                $customer->save();
            }
        }else{
            $customer = Customer::find($request->customer_id);
        }

        $order = PosOrder::create([
            'user_id' => $user->id,
            'business_name' => $user->business_name,

            'customer_id' => $customer->id,
            'customer_name' => $request->walkin_customer == 1 ? null : $customer->name,
            'customer_email' => $request->walkin_customer == 1 ? null : $customer->email,
            'customer_mobile' => $request->walkin_customer == 1 ? null : $customer->mobile,
            'customer_notes' => $request->walkin_customer == 1 ? null : $customer->notes,
            'bullding_no' => $request->walkin_customer == 1 ? null : $customer->bullding_no,
            'street_name' => $request->walkin_customer == 1 ? null : $customer->street_name,
            'district' => $request->walkin_customer == 1 ? null : $customer->district,
            'city' => $request->walkin_customer == 1 ? null : $customer->city,
            'postal_code' => $request->walkin_customer == 1 ? null : $customer->postal_code,
            'additional_no' => $request->walkin_customer == 1 ? null : $customer->additional_no,
            'other_buyer_id' => $request->walkin_customer == 1 ? null : $customer->other_buyer_id,
            'vat_registration_number' => $request->walkin_customer == 1 ? null : $customer->vat_registration_number,

            'add_discount' => $request->add_discount ?? false,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,

            'add_tax' => $request->add_tax ?? false,
            'tax_name' => $request->tax_name,
            'tax_value' => $request->tax_value,
            'payment_method' => $request->payment_method,
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


        $bill = DB::transaction(function () use ($order, $request, $authUser) {
            $user = User::find($order->user_id);

            $billStatus = '';
            $payment_way = null;
            switch ($order->payment_method) {
                case 'posPayOnline':
                    $billStatus = 'pending';
                    break;

                case 'posPayCard':
                    $billStatus = 'paid';
                    $payment_way = 'payment_machine';
                    break;

                case 'posPayCash':
                    $billStatus = 'paid_cash';
                    $payment_way = 'cash';
                    break;

                default:
                    break;
            }

            $bill = Bill::create([
                'user_id' => $user->id,
                'created_by' => $authUser->id,
                'status' => $billStatus,
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

                'add_discount' => $order->add_discount ?? false,
                'discount_type' => $order->add_discount  ? $order->discount_type : false,
                'discount_value' => $order->add_discount  ? $order->discount_value : null,

                'add_tax' => $order->add_tax ?? false,
                'tax_name' => $order->add_tax ? $order->tax_name : null,
                'tax_value' => $order->add_tax ? $order->tax_value : null,

                'send_sms' => ($request->walkin_customer == 1) ? false : true,
                'send_email' => ($request->walkin_customer == 1) ? false : true,

                'source' => 'pos',
                'payment_way' => $payment_way,
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

        if($bill->status == 'paid' || $bill->status == 'paid_cash'){
            event(new PosBillPaid($bill));
        }

        return new BillPosApiResource($bill);
    }

    public function getBills(){
        $authUser = auth('api')->user();

        $bills = Bill::createdBy($authUser->id)->source('pos')->orderBy('created_at', 'DESC')->paginate(20);

        $billsCollection = OrdersBillsPosApiResource::collection($bills);

        return $billsCollection;
    }

    public function getBill($id){
        $authUser = auth('api')->user();

        $bill = Bill::where('id', $id)->get();

        if($bill[0]->created_by == $authUser->id){
            $billCollection = OrderBillPosApiResource::collection($bill);
            $firstItem = $billCollection->first();
            return $firstItem;
        }else{
            return response()->json(['authorization' => 'not authorized to show this bill'], 403);
        }
    }

    public function sendBillByEmail(Request $request){
        $authUser = auth('api')->user();

        $bill = Bill::find($request->bill_id);

        if($bill->created_by == $authUser->id){
            event(new PosSendBill($bill, $request->email));
            return response()->json(['success' => 'bill sent successfully'], 200);
        }else{
            return response()->json(['authorization' => 'not authorized to show this bill'], 403);
        }
    }

    public function setUserSetting(Request $request)
    {
        $authUser = auth('api')->user();

        foreach($request->settings as $key => $setting){
            PosUserSetting::updateOrCreate([
                'user_id' => $authUser->id,
                'key' => $setting['key']
            ],
            [
                'key' => $setting['key'],
                'value' => $setting['value'],
                'enabled' => $setting['enabled'],
            ]);
        }

        $pos_settings = [];

        foreach($authUser->posUserSettings as $setting){
            $pos_settings[$setting->key] = [
                'key' => $setting->key,
                'value' => $setting->value,
                'enabled' => $setting->enabled,
            ];
        }
        return array($pos_settings);
    }

    public function redirectToBillsProducts(PosRedirectToBillsProductsRequest $request)
    {
        $authUser = auth('api')->user();
        if (Hash::check($request->password, $authUser->password))
        {
            $redirectUuid = \Str::uuid();
            $user = User::where('id', $authUser->id)->update(array('redirect_uuid' => $redirectUuid));
            return response()->json(['redirect_url' => route('redirect.to.products.via.pos', $redirectUuid)], 200);
        }

        return response()->json(['authorization' => 'invalid password'], 403);
    }
}
