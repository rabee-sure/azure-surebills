<?php

namespace App\Exports;

use App\Models\Bill;
use App\Models\RefundedBill;
use App\Models\Transaction;
use App\Models\Transfer;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransferBillsExportData implements FromQuery, WithHeadings, WithMapping, ShouldQueue
{
    use Exportable;

    public $transfer;

    public function __construct($transfer)
    {
        $this->transfer = $transfer;
    }

    public function headings(): array
    {
        return [
            __('Payment Date'),
            __('Description'),
            __('Reference'),
            __('Receipt'),
            __('Card'),
            __('Debit'),
            __('Credit'),
            __('Balance')
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->created_at,
            $transaction->description,
            $transaction->reference,
            $transaction->receipt,
            $transaction->card.' '.$transaction->card_brand,
            $transaction->debit,
            $transaction->credit,
            $transaction->balance,
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        $transactions = Transaction::select('id', 'created_at', 'description', 'reference', 'receipt', 'card', 'card_brand', DB::raw("(CASE WHEN type = 'credit' THEN amount ELSE 0 END) AS credit"), DB::raw("(CASE WHEN type = 'debit' THEN amount ELSE 0 END) AS debit"), 'balance')
                        ->join('transaction_transfer', 'transactions.id', 'transaction_transfer.transaction_id')
                        ->where('transaction_transfer.transfer_id', $this->transfer->id)
                        ->orderBy('transactions.created_at');

        return $transactions;
    }

}