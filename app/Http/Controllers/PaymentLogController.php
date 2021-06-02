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
        $this->authorize('view', $log);

        return view('bills.log', [
            'bill' => $log->bill,
            'log' => $log
        ]);
    }
}
