<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $all_transactions = $this->query($request)->get();
        $transactions = $this->query($request)->with('bill', 'bill.application', 'bill.application.channel')->paginate($request->get('per_page', 10));

        return TransactionResource::collection($transactions)->additional(['meta' => [
            'balance' => round($all_transactions->where('type', 'credit')->sum('amount')-$all_transactions->where('type', 'debit')->sum('amount'), 2),
            'total_credit' => round($all_transactions->where('type', 'credit')->sum('amount'), 2),
            'total_debit' => round($all_transactions->where('type', 'debit')->sum('amount'), 2),
        ]]);
    }
    
    protected function query ($request){
        $user = $request->user;
        $date_start = $request->date_start ?? Carbon::today()->firstOfMonth()->format('m/d/Y');
        $date_to = $request->date_to ?? Carbon::today()->format('m/d/Y');
        $transaction_type = $request->transaction_type ?? null;
        $transaction_source = $request->transaction_source ?? null;
        $channel = $request->channel ?? null;

        return $user->statement()
        ->when($date_start, function($q) use($date_start, $date_to){
            $q->whereDate('created_at', '>=', Carbon::parse($date_start))
                ->whereDate('created_at', '<=', Carbon::parse($date_to));
        })
        ->when($transaction_type == 'debit' || $transaction_type == 'credit', function($q) use($transaction_type){
            $q->whereType($transaction_type);
        })
        ->when(isset($transaction_source) && $transaction_source != 'all' && $transaction_source != 'undefined', function($q) use($transaction_source){
            $q->whereTransactionSource($transaction_source);
        })
        ->when($channel, function($q) use($channel){
            $q->whereHas('bill.application', function ( $query) use($channel){
                $query->where('channel_id', $channel->id);
            });
        });
    }
}
