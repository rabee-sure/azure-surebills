<?php

namespace App\Exports;

use App\Transaction;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class BillsExport implements FromView, ShouldQueue
{
    use Exportable;
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
