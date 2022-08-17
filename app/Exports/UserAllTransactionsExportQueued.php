<?php

namespace App\Exports;

use App\Http\Resources\TransactionResource;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UserAllTransactionsExportQueued implements FromView, ShouldQueue
{
    use Exportable;

    protected $user, $cycle_date;

    public function __construct( $user, $cycle_date)
    {
        $this->user = $user;
        $this->cycle_date = $cycle_date;
    }

    public function view(): View
    {
        $transactions = $this->user->transactions()
            ->amountByCycleDate($this->cycle_date)
            ->orderBy('created_at', 'ASC')
            ->orderBy('order', 'ASC')
            ->orderBy('receipt', 'ASC')
            ->with(['bill.application'])
            ->get();

        return view('exports.user_all_transactions', [
            'transactions' => TransactionResource::collection($transactions)
        ]);
    }
}