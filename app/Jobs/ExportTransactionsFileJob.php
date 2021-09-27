<?php

namespace App\Jobs;

use App\Exports\TransactionsExport;
use App\Http\Resources\TransactionExportResource;
use App\Models\PaymentLog;
use App\Models\Transaction;
use App\Models\Transfer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExportTransactionsFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $transfer_ids;
    protected $file_name;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($transfer_ids, $file_name )
    {
        $this->transfer_ids = $transfer_ids;
        $this->file_name = $file_name;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $channel_sources = [
            'channel_extra_amount',
            'channel_extra_amount_vat',
            'channel_extra_amount_fees',
            'channel_fees',
            'channel_vat',
        ];

        $transactions = Transaction::whereHas('transfers', function($q) use( $channel_sources){
            $q->whereIn('transfer_id', $this->transfer_ids)->whereIn('transaction_source', $channel_sources);
        })->with('bill.application.channel')->get();
        $data = json_decode((TransactionExportResource::collection($transactions))->toJson(), true);

        (new TransactionsExport($data))->store($this->file_name, 'public');
    }
}
