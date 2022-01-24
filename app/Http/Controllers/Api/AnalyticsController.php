<?php

namespace App\Http\Controllers\Api;

use App\Models\Bill;
use App\Http\Controllers\Controller;
use App\Models\Transfer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
        $refunded_bills = Bill::whereBetween('refunded_at', [$from, $to])->refunded()->get();
         
        $sum_total = $paid_bills->sum('total');
        $sum_surebills_fees = $paid_bills->sum('payment_surebills_fees');
        $sum_surebills_fees_vat = $paid_bills->sum('payment_surebills_fees_vat');

        $paid_bills_not_settled =  $paid_bills->where('settled', false);
        $total_due_merchants = $paid_bills_not_settled->sum('total') - $paid_bills_not_settled->sum('payment_surebills_fees') - $paid_bills_not_settled->sum('payment_surebills_fees_vat');
        $total_transfers_merchants = Transfer::whereBetween('created_at', [$from, $to])->sum('amount');

     
        $filter = $this->encode([
            [    
                "class"=> "PosLifestyle\DateRangeFilter\DateRangeFilter_created_at",
                "value" => [$from->format('Y-m-d'), $to->format('Y-m-d')]
            ]
        ]);
        $filter2 = $this->encode([
            [    
                "class"=> "PosLifestyle\DateRangeFilter\DateRangeFilter_paid_at",
                "value" => [$from->format('Y-m-d'), $to->format('Y-m-d')]
            ],
            [    
                "class"=> "App\Nova\Filters\BillStatus",
                "value" => ["paid"]
            ]
        ]);
        $filter4 = $this->encode([
            [    
                "class"=> "PosLifestyle\DateRangeFilter\DateRangeFilter_refunded_at", 
                "value" => [$from->format('Y-m-d'), $to->format('Y-m-d')]
            ],
            [    
                "class"=> "App\Nova\Filters\BillStatus",
                "value" => ["refunded"]
            ]
        ]);
        $filter3 = $this->encode([
            [    
                "class"=> "PosLifestyle\DateRangeFilter\DateRangeFilter_paid_at",
                "value" => [$from->format('Y-m-d'), $to->format('Y-m-d')]
            ],
            // [    
            //     "class"=> "App\Nova\Filters\BillSettled",
            //     "value" => 1
            // ]
        ]);           

        $filter5 = $this->encode([
            [    
                "class" => "PosLifestyle\DateRangeFilter\DateRangeFilter_paid_at",
                "value" => [$from->format('Y-m-d'), $to->format('Y-m-d')]
            ],
            // [    
            //     "class" => "App\Nova\Filters\BillSettled",
            //     "value" => 2
            // ],
            [    
                "class" => "App\Nova\Filters\BillStatus",
                "value" => ["paid"]
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
                'refunded_bills' => [
                    'count' =>  $refunded_bills->count(),
                    'filter' =>  $filter4,
                    'link' =>  '/nova/resources/bills?bills_page=1&bills_filter='.$filter4,
                ],
                'total_transactions' => [
                    'count' =>  round($sum_total, 2),
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
                    'filter' =>  $filter5,
                    'link' =>  '/nova/resources/bills?bills_page=1&bills_filter='.$filter5,
                ],
                'total_transfers_merchants' => [
                    'count' =>  round($total_transfers_merchants, 2),
                    'filter' =>  $filter,
                    'link' =>  '/nova/resources/transfers?transfers_page=1&transfers_filter='.$filter,
                ],
            ],
            'date' =>  [$from->format('Y-m-d'), $to->format('Y-m-d')]
        ]);
    }

    protected function encode($data)
    {
        return base64_encode(json_encode($data));
    }
}
