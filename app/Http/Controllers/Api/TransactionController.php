<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\UserStatResource;
use App\Models\Application;
use App\Models\Settings;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        $user = $request->user;
        $date_start = $request->date_start ?? Carbon::today()->firstOfMonth()->format('m/d/Y');
        $date_to = $request->date_to ?? Carbon::today()->format('m/d/Y');
        $transaction_type = $request->transaction_type ?? null;
        $transaction_source = $request->transaction_source ?? null;
        $channel = $request->channel ?? null;
        $transactions =  $user->statement()
            ->when($date_start, function($q) use($date_start, $date_to){
                $q->whereDate('created_at', '>=', Carbon::parse($date_start))
                    ->whereDate('created_at', '<=', Carbon::parse($date_to));
            })
            ->when(request()->transaction_type == 'debit' || request()->transaction_type == 'credit', function($q) {
                $q->whereType(request()->transaction_type);
            })
            ->when(isset(request()->transaction_source) && request()->transaction_source != 'all' && request()->transaction_source != 'undefined', function($q){
                $q->whereTransactionSource(request()->transaction_source);
            })
            ->when($channel, function($q) use($channel){
                $q->whereHas('bill.application', function ( $query) use($channel){
                    $query->where('channel_id', $channel->id);
                });
            })
            ->get();

        return TransactionResource::collection($transactions)->additional(['meta' => [
            'balance' => round($transactions->where('type', 'credit')->sum('amount')-$transactions->where('type', 'debit')->sum('amount'), 2),
            'total_credit' => round($transactions->where('type', 'credit')->sum('amount'), 2),
            'total_debit' => round($transactions->where('type', 'debit')->sum('amount'), 2),
        ]]);
    }
}
