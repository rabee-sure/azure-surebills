<?php

namespace App\Http\Controllers;

use App\Models\RefundedBill;
use Illuminate\Http\Request;

class RefundedBillController extends Controller
{
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
     * Store a newly created resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $refundedbill
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $refundedBill = RefundedBill::find($id);
        return view('bills.refunded_bill_show', ['refundedbill' => $refundedBill]);
    }

    public function billPrint($id, Request $request)
    {
        $refundedBill = RefundedBill::find($id);
        $type = $request->input('type');
        $lang = $request->input('lang');
        if($type == 'billA4' && $lang == 'en'){
          return view('refunded_bills.print_template.a4_en', compact('refundedBill', 'lang'));
        }elseif($type == 'billA4' && $lang == 'ar'){
          return view('refunded_bills.print_template.a4_ar', compact('refundedBill', 'lang'));
        }elseif($type == 'billTh' && $lang == 'en'){
          return view('refunded_bills.print_template.th_en', compact('refundedBill', 'lang'));
        }elseif($type == 'billTh' && $lang == 'ar'){
          return view('refunded_bills.print_template.th_ar', compact('refundedBill', 'lang'));
        }
    }

    public function invoice($id, $lang = null)
    {
        $refundedBill = RefundedBill::decodeId($id);
        return view('refunded_bills.invoice', compact('refundedBill', 'id'));
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
     * Update the specified resource .
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
     * Remove the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
