<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MerchantTransactionsExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    private $user_id;
    private $from;
    private $to;

    public function __construct($user_id, $from = null, $to = null)
    {
        $this->user_id = $user_id;
        $this->from = $from;
        $this->to = $to;
    }

    public function headings(): array
    {
        return [
            __('Created At'),
            __('Description'),
            __('Reference'),
            __('Receipt'),
            __('Auth ID'),
            __('Card Brand'),
            __('Type'),
            __('Amount'),
            __('Balance'),
            __('Merchant')
        ];
    }

    public function map($transaction): array
    {
        return[
            $transaction->created_at,
            $transaction->description,
            $transaction->reference,
            $transaction->receipt,
            $transaction->auth_id,
            $transaction->card_brand,
            $transaction->type,
            $transaction->amount,
            $transaction->balance,
            $transaction->user->name
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        $query = Transaction::where('user_id', $this->user_id)
        ->select(
            'created_at',
            'description',
            'reference',
            'receipt',
            'auth_id',
            'card_brand',
            'type',
            'amount',
            'balance',
            'user_id'
        );

        if($this->from != null){
            $query->where('created_at', '>=', $this->from);
        }

        if($this->to != null){
            $query->where('created_at', '<=', $this->to);
        }

        $query->orderBy('created_at');

        return $query;
    }
}
