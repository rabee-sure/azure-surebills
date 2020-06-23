<?php

namespace App\Http\Controllers;

use App\Bill;
use App\BillItem;
use App\Customer;
use App\Events\BillCreated;
use App\Http\Requests\BillRequest;
use App\Http\Requests\PayBillRequest;
use App\Payment\Facades\Payment;
use App\Payment\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BillController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $bills = Bill::where('user_id', auth()->user()->id)->get();
        return view('bills.index', ['bills' => $bills]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('bills.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BillRequest $request)
    {
        // dd($request->all());
        $customer = Customer::firstOrCreate([
            'name' => $request->customer_name, 
            'email' => $request->customer_email,
            'mobile' => $request->customer_mobile,
            'user_id' => auth()->user()->id,
        ]);

        $bill = Bill::create([
            'user_id' => auth()->user()->id,
            'business_name' => auth()->user()->business_name,
            'customer_id' => $customer->id,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_mobile' => $request->customer_mobile,
            'customer_notes' => $request->customer_notes,

            'expiry_date' => $request->expiry_date,
            'due_date' => Carbon::parse($request->due_date),

            'add_discount' => $request->add_discount,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,

            'add_tax' => $request->add_tax,
            'tax_name' => $request->tax_name,
            'tax_value' => $request->tax_value,

            'send_sms' => $request->send_sms,
            'send_email' => $request->send_email,
        ]);

        foreach ($request->items as $item) {
            BillItem::create([
                'bill_id' => $bill->id,
                'product_name' => $item['name'],
                'product_price' => $item['price'],
                'quantity' => $item['quantity'],
                'total' => $item['quantity']*$item['price'],
            ]);
        }

        $sub_total = $bill->items->sum('total');
        $discount = 0;
        $vat = $request->add_tax ? $request->tax_value : 0;
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

        $bill->discount = $discount;
        $bill->vat = $vat;
        $bill->sub_total = $sub_total;
        $bill->total = $sub_total - $discount + $vat;
        $bill->save();
        
        event(new BillCreated($bill));

        return redirect()->route('bills.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('bills.show');
    }

    /**
     * Display the payment page for a specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function pay($id)
    {
        $bill = Bill::decodeId($id);
        if($bill == null || $bill->is_invalid){
            return view('bills.status', ['bill' => $bill]);
            // abort(404);
        }
        return view('bills.pay', ['bill' => $bill, 'id'=> $id]);
    }
    
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function postPay($id, PayBillRequest $request)
    {
        $bill = Bill::decodeId($id);

        $expiry = $request->get('expiry', '03/21');
        $expiry_array = str_replace(' ', '', explode("/", $expiry));
        $credit_card = str_replace(' ', '', $request->get('number', '4242424242424242'));

        $invoice = (new Invoice)->amount($bill->total );
        $invoice->detail(['name' => $request->get('name')])
                ->detail(['number' => $credit_card])
                ->detail(['expiry' => $expiry])
                ->detail(['expiry_month' => $expiry_array[0]])
                ->detail(['expiry_year' => $expiry_array[1]])
                ->detail(['bill' => $bill->toArray()])
                ->detail(['cvc' => $request->get('cvc')]);
        // Purchase the given invoice.
        Payment::purchase($invoice, function($driver, $transactionId) use($bill){
            $bill->status = 'paid';
            $bill->paid_at = Carbon::now();
            $bill->payment_method = 'credit';
            $bill->save();
        });
        return view('bills.status', ['bill' => $bill]);;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
