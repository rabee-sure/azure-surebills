<?php

namespace App\Exports;

use App\Transaction;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;

class TransactionsExportQueued implements FromView, ShouldQueue
{
    use Exportable;

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