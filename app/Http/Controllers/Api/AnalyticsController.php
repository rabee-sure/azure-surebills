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

        $paid_bills = Bill::whereBetween('paid_at', [$from, $to])->paid()->get();
        $sum_total = $paid_bills->sum('total');
        $sum_surebills_fees = $paid_bills->sum('payment_surebills_fees');
        $sum_surebills_fees_vat = $paid_bills->sum('payment_surebills_fees_vat');

        $paid_bills_not_settled =  $paid_bills->where('settled', false);
        $total_due_merchants = $paid_bills_not_settled->sum('total') - $paid_bills_not_settled->sum('payment_surebills_fees') - $paid_bills_not_settled->sum('payment_surebills_fees_vat');
        $total_transfers_merchants = Transfer::whereBetween('created_at', [$from, $to])->sum('amount');

     
        $filter = $this->encode([
            [    
                "class"=> "App\Nova\Filters\DateRange",
                "value" => [$from->format('Y-m-d'), $to->format('Y-m-d')]
            ]
        ]);
        $filter2 = $this->encode([
            [    
                "class"=> "App\Nova\Filters\PaidDateRange",
                "value" => [$from->format('Y-m-d'), $to->format('Y-m-d')]
            ],
            [    
                "class"=> "App\Nova\Filters\BillStatus",
                "value" => ["paid"]
            ]
        ]);
        $filter3 = $this->encode([
            [    
                "class"=> "App\Nova\Filters\PaidDateRange",
                "value" => [$from->format('Y-m-d'), $to->format('Y-m-d')]
            ],
            [    
                "class"=> "App\Nova\Filters\BillSettled",
                "value" => [1]
            ]
        ]);   
        return response()->json([
            'data' => [
                'users' => [
                    'count' =>  User::whereBetween('created_at', [$from, $to])->count(),
                     'filter' =>  $filter,
                     'link' =>  '/nova/resources/users?users_page=1&users_filter='.$filter,
                ],
                'bills' => [
                    'count' =>  Bill::whereBetween('created_at', [$from, $to])->count(),
                    'filter' =>  $filter,
                    'link' =>  '/nova/resources/bills?bills_page=1&bills_filter='.$filter,
                ],
                'successful_bills' => [
                    'count' =>  $paid_bills->count(),
                    'filter' =>  $filter2,
                    'link' =>  '/nova/resources/bills?bills_page=1&bills_filter='.$filter2,
                ],
                'total_transactions' => [
                    'count' =>  $sum_total,
                    'filter' =>  $filter,
                    'link' =>  '/nova/resources/bills?bills_page=1&bills_filter='.$filter2,
                ],
                'surebills_fees' => [
                    'count' =>  round($sum_surebills_fees, 2),
                    'filter' =>  $filter,
                    'link' =>  '/nova/resources/bills?bills_page=1&bills_filter='.$filter2,
                ],
                'surebills_fees_vat' => [
                    'count' =>  round($sum_surebills_fees_vat, 2),
                    'filter' =>  $filter,
                    'link' =>  '/nova/resources/bills?bills_page=1&bills_filter='.$filter2,
                ],
                'total_due_merchants' => [
                    'count' =>  round($total_due_merchants, 2),
                    'filter' =>  $filter3,
                    'link' =>  '/nova/resources/bills?bills_page=1&bills_filter='.$filter3,
                ],
                'total_transfers_merchants' => [
                    'count' =>  round($total_transfers_merchants, 2),
                    'filter' =>  $filter,
                    'link' =>  '/nova/resources/transfers?transfers_page=1&transfers_filter='.$filter,
                ],
            ]
        ]);
    }

    protected function encode($data)
    {
        return base64_encode(json_encode($data));
    }
}
