<?php

namespace App\Http\Controllers;


use App\Models\PaymentLog;
use Carbon\Carbon;
use Illuminate\Http\Request;


class PaymentLogController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(PaymentLog $log)
    {
        $log = $log->whereHas('bill', function($q){
            $q->whereIn('user_id', auth()->user()->storeUsers(true));
        })->where('id', $log->id)->first();

        if(!$log)
        {
            $this->authorize('view', $log);
        }

        return view('bills.log', [
            'bill' => $log->bill,
            'log' => $log
        ]);
    }
}
