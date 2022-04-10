<?php

namespace App\Exports;

use App\Models\AutoTransfer;
use App\Models\DueAmountAutoTransferReport;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;

class DueAmountsExport implements FromView
{
    use Exportable;

    protected $items;

    public function __construct($items)
    {
        $this->items = $items;
        $this->store();
    }

    /**
    * @return \Illuminate\Support\View
    */
    public function view(): View
    {
        return view('exports.due_amounts', [
            'items' => $this->items
        ]);
    }

    private function store()
    {
        foreach($this->items as $item)
        {
            DueAmountAutoTransferReport::create([
                'auto_transfer_id' => AutoTransfer::max('id'),
                'merchant_id' => $item['merchant_id'],
                'merchant_name' => $item['merchant_name'],
                'merchant_iban' => $item['merchan_iban'],
                'bank' => $item['bank'],
                'total_amount' => $item['total_amount'],
                'total_fees' => $item['total_fees'],
                'total_fees_vat' => $item['total_fees_vat'],
                'total_refund' => $item['total_refund'],
                'bank_charges' => $item['bank_charges'],
                'net_due' => $item['net_due'],
                'channel_id' => $item['channel_id'],
                'reference' => $item['transfer_id'],
            ]);
        }
    }
}
