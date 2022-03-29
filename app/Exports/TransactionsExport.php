<?php

namespace App\Exports;

use App\Models\AutoTransfer;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Models\MerchantAutoTransferReport;

class TransactionsExport implements FromView
{
    use Exportable;

    protected $transactions, $reportType;

    public function __construct( $transactions, $reportType = null)
    {
        $this->transactions = $transactions;
        $this->reportType = $reportType;
        $this->store();
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

    private function store()
    {
        foreach($this->transactions as $transaction)
        {
            if($this->reportType)
            {
                $transaction['transaction_id'] = $transaction['id'];
                $transaction['report_type'] = $this->reportType;
                $transaction['auto_transfer_id'] = AutoTransfer::max('id');
                unset($transaction['id']);
                MerchantAutoTransferReport::create($transaction);
            }
        }
    }
}
