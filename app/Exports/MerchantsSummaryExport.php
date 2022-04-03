<?php

namespace App\Exports;

use App\Models\AutoTransfer;
use App\Models\MerchantSummaryAutoTransferReport;
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
        $this->store();
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

    private function store()
    {
        foreach($this->items as $item)
        {
            MerchantSummaryAutoTransferReport::create([
                'auto_transfer_id' => AutoTransfer::max('id'),
                'client_id' => $item['client_id'],
                'payment_type' => $item['payment_type'],
                'no_of_trx' => $item['no_of_trx'],
                'total_amount' => $item['total_amount'],
                'total_fees' => $item['total_fees'],
                'total_fees_vat' => $item['total_fees_vat'],
                'total_fees_variable_rate' => $item['total_fees_variable_rate'],
                'total_fees_fixed_rate' => $item['total_fees_fixed_rate'],
                'sure_variable_rate' => $item['sure_variable_rate'],
                'sure_fixed_rate' => $item['sure_fixed_rate'],
                'channel_variable_rate' => $item['channel_variable_rate'],
                'channel_fixed_rate' => $item['channel_fixed_rate'],
                'sure_fees' => $item['sure_fees'],
                'sure_vat' => $item['sure_vat'],
                'channel_fees' => $item['channel_fees'],
                'channels_vat' => $item['channels_vat'],
                'channel_id' => $item['channel_id'],
            ]);
        }
    }
}
