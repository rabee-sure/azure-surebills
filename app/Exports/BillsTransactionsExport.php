<?php

namespace App\Exports;

use App\Models\Bill;
use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BillsTransactionsExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public $options;
    
    public function __construct($options)
    {
        $this->options = $options;
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
            __('Balance'),
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        $billsIds = self::getBillsIds();

        $query = Transaction::select('created_at', 'description', 'reference', 'receipt', 'card', 'type', 'amount', 'balance')
        ->whereIn('bill_id', $billsIds)
        ->orderBy('created_at');

        return $query;
    }

    public function map($transaction): array
    {
        return [
            $transaction->created_at,
            $transaction->description,
            $transaction->reference,
            $transaction->receipt,
            $transaction->card,
            $transaction->type == 'debit' ? round2($transaction->amount) : '-',
            $transaction->type == 'credit' ? round2($transaction->amount) : '-',
            fact_number(round($transaction->balance, 2)),
        ];
    }

    public function getBillsIds(){
        $billsIds = Bill::select('id');

        if(!empty($this->options['bills_ids'])){
            $billsIds = $billsIds->whereIn('id', $this->options['bills_ids']);
        }else{
            if($this->options['user_id'] != null){
                $billsIds = $billsIds->where('user_id', $this->options['user_id']);
            }
    
            if($this->options['from'] != null){
                $billsIds = $billsIds->whereDate($this->options['period_column'], '>=', $this->options['from']);
            }
    
            if($this->options['to'] != null){
                $billsIds = $billsIds->whereDate($this->options['period_column'], '<=', $this->options['to']);
            }
        }

        $billsIds = $billsIds->pluck('id')->toArray();

        return $billsIds;
    }
}
