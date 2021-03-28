<?php

namespace App\Exports;

use App\Transaction;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class BillsExport implements FromView
{
    protected $bills;

    public function __construct( $bills)
    {
        $this->bills = $bills;
    }

    /**
    * @return \Illuminate\Support\View
    */
    public function view(): View
    {
        return view('exports.bills', [
            'bills' => $this->bills
        ]);
    }
}
