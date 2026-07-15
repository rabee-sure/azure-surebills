<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\Transfer;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    /**
     * Store a newly created resource .
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

        $balance_total = $transfer->transactions()
            ->select(DB::raw("SUM(CASE WHEN type  = 'credit' THEN amount ELSE 0 END) AS credit_total,SUM(CASE WHEN type  = 'debit' THEN amount ELSE 0 END) AS debit_total"))
            ->first();
        $transactions = $transfer->transactions()->paginate($request->get('per_page', 10));
        return TransactionResource::collection($transactions)->additional(['meta' => [
            'balance' => round2($balance_total->credit_total - $balance_total->debit_total),
            'total_credit' => round2($balance_total->credit_total),
            'total_debit' => round2($balance_total->debit_total),
        ]]);
    }
}
