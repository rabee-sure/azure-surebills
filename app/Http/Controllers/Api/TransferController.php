<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ValidationException;
use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\Application;
use App\Models\Transfer;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
        $transactions = $transfer->transactions;
        return TransactionResource::collection($transactions)->additional(['meta' => [
            'balance' => round($transactions->where('type', 'credit')->sum('amount')-$transactions->where('type', 'debit')->sum('amount'), 2),
            'total_credit' => round($transactions->where('type', 'credit')->sum('amount'), 2),
            'total_debit' => round($transactions->where('type', 'debit')->sum('amount'), 2),
        ]]);
    }
}
