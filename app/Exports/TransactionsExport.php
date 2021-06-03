<?php

namespace App\Exports;

use App\Transaction;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TransactionsExport implements FromView
{
    protected $transactions;

    public function __construct( $transactions)
    {
        $this->transactions = $transactions;
    }

    /**
    * @return \Illuminate\Support\View
    */
    public function view(): View
    {
        return view('exports.transactions', [
            'transactions' => $this->transactions
        ]);
    }
}