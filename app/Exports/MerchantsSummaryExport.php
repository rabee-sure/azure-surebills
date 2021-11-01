<?php

namespace App\Exports;

use App\Transaction;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;

class MerchantsSummaryExport implements FromView
{
    use Exportable;

    protected $items;

    public function __construct( $items)
    {
        $this->items = $items;
    }

    /**
    * @return \Illuminate\Support\View
    */
    public function view(): View
    {
        return view('exports.merchants_summary', [
            'items' => $this->items
        ]);
    }
}