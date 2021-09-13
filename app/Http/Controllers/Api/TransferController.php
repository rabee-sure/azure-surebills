<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\Transfer;
use Illuminate\Http\Request;

class TransferController extends Controller
{   
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Transfer  $transfer
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function transactions(Transfer $transfer, Request $request)
    {
        if($transfer->user_id !== $request->user->id){
            return response('Unauthorized.', 401);
        }

        $transactions_all = $transfer->transactions()->get();
        $transactions = $transfer->transactions()->paginate($request->get('per_page', 10));
        return TransactionResource::collection($transactions)->additional(['meta' => [
            'balance' => round($transactions_all->where('type', 'credit')->sum('amount')-$transactions_all->where('type', 'debit')->sum('amount'), 2),
            'total_credit' => round($transactions_all->where('type', 'credit')->sum('amount'), 2),
            'total_debit' => round($transactions_all->where('type', 'debit')->sum('amount'), 2),
        ]]);
    }
}
