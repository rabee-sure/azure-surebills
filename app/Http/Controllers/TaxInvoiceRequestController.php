<?php

namespace App\Http\Controllers;

use App\Jobs\SendTaxInvoiceRequestMailJob;
use App\Models\TaxInvoiceRequest;
use Illuminate\Http\Request;

class TaxInvoiceRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:update settings', ['only' => ['store']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function store(Request $request)
    {
        $user = auth()->user()->mainStoreUser ?? auth()->user();

        if($user->hasPendingTaxInvoiceRequest()){
            return redirect()->back()->withErrors([__('You have pending Request! please wait.')]);
        }

        $taxInvoiceRequest = TaxInvoiceRequest::create([
            "user_id" => $user->id,
            "status" => "pending"
        ]);

        SendTaxInvoiceRequestMailJob::dispatch($user);

        return redirect()->back()->with('message', __('Your request has been sent succefully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TaxInvoiceRequest  $taxInvoiceRequest
     * @return \Illuminate\Http\Response
     */
    public function show(TaxInvoiceRequest $taxInvoiceRequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TaxInvoiceRequest  $taxInvoiceRequest
     * @return \Illuminate\Http\Response
     */
    public function edit(TaxInvoiceRequest $taxInvoiceRequest)
    {
        //
    }

    /**
     * Update the specified resource .
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TaxInvoiceRequest  $taxInvoiceRequest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TaxInvoiceRequest $taxInvoiceRequest)
    {
        //
    }

    /**
     * Remove the specified resource.
     *
     * @param  \App\Models\TaxInvoiceRequest  $taxInvoiceRequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(TaxInvoiceRequest $taxInvoiceRequest)
    {
        //
    }
}
