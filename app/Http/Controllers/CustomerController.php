<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Requests\CustomerRequest;
use App\Http\Requests\CustomerUpdateRequest;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $customers = auth()->user()->customers()
            ->orderBy('id', 'desc')
            ->paginate($request->get('per_page', 10));
        return view('customers.index',  ['customers' => $customers]);
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
            ->where('user_id', auth()->user()->id)
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
            ->where('user_id', auth()->user()->id)
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
     * Store a newly created resource in storage.
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
            'user_id' => auth()->user()->id,
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
        return view('customers.edit', ['customer' => $customer]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(CustomerUpdateRequest $request, Customer $customer)
    {
        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->mobile = $request->mobile;
        $customer->notes = $request->notes;
        $customer->save();

        return redirect()->route('customers.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index');
    }
}
