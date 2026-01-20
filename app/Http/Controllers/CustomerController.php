<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Requests\CustomerRequest;
use App\Http\Requests\CustomerUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:show customers', ['only' => ['index','show', 'searchByName', 'searchByMobile']]);
        $this->middleware('permission:create customer', ['only' => ['create','store']]);
        $this->middleware('permission:update customer', ['only' => ['edit','update']]);
        $this->middleware('permission:delete customer', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $customers = Customer::userId(auth()->user()->store_main_user_id ?? auth()->user()->id)
            ->walkinCustomer(0)
            ->orderBy('id', 'desc')
            ->with('bills')
            ->paginate($request->get('per_page', 10));
        $user = User::find(auth()->user()->store_main_user_id ?? auth()->user()->id);
        return view('customers.index',  ['customers' => $customers, 'user' => $user]);
    }

    /**
     * search By Name.
     *
     * @return \Illuminate\Http\Response
     */
    public function searchByName(Request $request)
    {
        $search = $request->get('search');
        $result = Customer::where('name', 'LIKE', '%'. $search. '%')
            ->userId(auth()->user()->store_main_user_id ?? auth()->user()->id)
            ->orderBy('id', 'desc')
            ->get();
        return response()->json($result);
    }

    /**
     * search By Name.
     *
     * @return \Illuminate\Http\Response
     */
    public function searchByMobile(Request $request)
    {
        $search = $request->get('search');
        $result = Customer::where('mobile', 'LIKE', '%'. $search. '%')
            ->userId(auth()->user()->store_main_user_id ?? auth()->user()->id)
            ->orderBy('id', 'desc')
            ->get();
        return response()->json($result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource .
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerRequest $request)
    {
        Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'notes' => $request->notes,
            'user_id' => auth()->user()->store_main_user_id ?? auth()->user()->id,

            'bullding_no' => $request->bullding_no,
            'street_name' => $request->street_name,
            'district' => $request->district,
            'city' => $request->city,
            'postal_code' => $request->postal_code,
            'additional_no' => $request->additional_no,
            'other_buyer_id' => $request->other_buyer_id,
            'vat_registration_number' => $request->vat_registration_number,
        ]);

        return redirect()->route('customers.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        $this->authorize('update', $customer);
        $user = User::find(auth()->user()->store_main_user_id ?? auth()->user()->id);
        return view('customers.edit', ['customer' => $customer, 'user' => $user]);
    }

    /**
     * Update the specified resource .
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(CustomerUpdateRequest $request, Customer $customer)
    {
        $this->authorize('update', $customer);
        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->mobile = $request->mobile;
        $customer->notes = $request->notes;
        $customer->bullding_no = $request->bullding_no;
        $customer->street_name = $request->street_name;
        $customer->district = $request->district;
        $customer->city = $request->city;
        $customer->postal_code = $request->postal_code;
        $customer->additional_no = $request->additional_no;
        $customer->other_buyer_id = $request->other_buyer_id;
        $customer->vat_registration_number = $request->vat_registration_number;
        $customer->save();

        return redirect()->route('customers.index');
    }

    /**
     * Remove the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        $this->authorize('update', $customer);
        $customer->delete();
        return redirect()->route('customers.index');
    }
}
