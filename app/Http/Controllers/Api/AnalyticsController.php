<?php

namespace App\Http\Controllers\Api;

use App\Application;
use App\Bill;
use App\Events\UserCreated;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserStatResource;
use App\Transfer;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AnalyticsController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function index(Request $request)
    {
        $from = Carbon::parse($request->from)->addHours(2)->startOfDay();
        $to = Carbon::parse($request->to)->addHours(2)->endOfDay();

        // dd($to);
        $paid_bills = Bill::whereBetween('created_at', [$from, $to])->paid();
        $sum_total = $paid_bills->sum('total');
        $sum_surebills_fees = $paid_bills->sum('payment_surebills_fees');
        $sum_surebills_fees_vat = $paid_bills->sum('payment_surebills_fees_vat');
        $total_transfers_merchants = Transfer::whereBetween('created_at', [$from, $to])->sum('amount');
        $total_due_merchants = $sum_total - $sum_surebills_fees_vat - $sum_surebills_fees - $total_transfers_merchants;
        return response()->json([
            'data' => [
                'users' => User::whereBetween('created_at', [$from, $to])->count(),
                'bills' => Bill::whereBetween('created_at', [$from, $to])->count(),
                'successful_bills' => Bill::whereBetween('created_at', [$from, $to])->paid()->count(),
                'total_transactions' => $sum_total,
                'surebills_fees' => $sum_surebills_fees,
                'surebills_fees_vat' => $sum_surebills_fees_vat,
                'total_transfers_merchants' => round($total_transfers_merchants, 2),
                'total_due_merchants' => round($total_due_merchants, 2),
            ]
        ]);
    }
}
