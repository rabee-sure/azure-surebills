<?php

namespace App\Console\Commands;

use App\Events\TransferCreated;
use App\Exports\BillsExport;
use App\Exports\TransactionsExport;
use App\Http\Resources\BillResource;
use App\Http\Resources\TransactionExportResource;
use App\Jobs\ExportTransactionsFileJob;
use App\Jobs\SendAutoTransferMailsJob;
use App\Jobs\ZipFolderJob;
use App\Mail\AutoTransferMail;
use App\Models\Bill;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\User;
use App\Services\TransferService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Valuestore\Valuestore;

class TransferAutomatic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:automatic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'transfer automatic';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $settings =  Valuestore::make(storage_path('app/settings.json'));
        $transfer_automatic = $settings->get('transfer_automatic');
        $transfer_day = $settings->get('transfer_day');
        $transfer_minimum = $settings->get('transfer_minimum');
        $transfer_emails = $settings->get('transfer_emails');

        $cycleDate = Carbon::now()->startOfDay();
        if($transfer_automatic && $cycleDate->dayOfWeek == $transfer_day ){
            $users = User::where('verified', true)->where('auto_trnasfer', true)->with('bank')->get();
            
            $filtered_users = $users->filter(function($user) use($transfer_minimum){
                return $user->actual_balance >= $transfer_minimum;
            });
            $transfer_ids = [];
            foreach ($filtered_users as $user) {
                $amount = $user->getBalanceBefore($cycleDate->format('Y-m-d'));
                if($amount  >= $transfer_minimum){
                    $this->info("transfer to user ID $user->id amount: $amount");

                    $bank = $user->bank;
                    $transfer_fees = $bank->fees + ($bank->fees * 0.15);
                    $data = [
                        'cycle_date' => $cycleDate,
                        'transfer_fees' => $transfer_fees,
                        'note' => 'automatic transfer',
                        'created_by_id' => null,
                        'bank_id' => $bank->id,
                        'user_id' => $user->id,
                        'iban_number' => $user->iban_number,
                        'beneficiary_name' => $user->beneficiary_name,
                    ];
                    $transfer = TransferService::makeTransfer('pending', $amount, $data);
                    $transfer_ids[] = $transfer->id;
                }
            }

            $this->createMasterSheet($transfer_ids, $cycleDate);
        }
    }
 
    /**
     * create Transactions Excel.
     *
     * @param  App\Transfer  $transfer
     * @return App\Transfer  $transfer
     */
    public function createMasterSheet($transfer_ids, $cycleDate)
    {
        $channel_sources = [
            'channel_extra_amount',
            'channel_extra_amount_vat',
            'channel_extra_amount_fees',
            'channel_fees',
            'channel_vat',
        ];

        if(count($transfer_ids)){
            $transactions = Transaction::whereHas('transfers', function($q) use($transfer_ids, $channel_sources){
                $q->whereIn('transfer_id', $transfer_ids)->whereNotIn('transaction_source', $channel_sources);
            })->with('bill.application.channel')->get();



            $day = $cycleDate->format('Y-m-d');
            $merchants_file = "automatic_transfers/$day/merchants_transactions.xlsx";
            $channels_file = "automatic_transfers/$day/channels_transactions.xlsx";

            $data = json_decode((TransactionExportResource::collection($transactions))->toJson(), true);
            (new TransactionsExport($data))->store($merchants_file, 'public')->chain([
               new ExportTransactionsFileJob($transfer_ids, $channels_file),
               new ZipFolderJob("automatic_transfers/$day", "master_sheet_$day.zip"),
               new SendAutoTransferMailsJob($day),
            ]);
        }
        return true;
    }
}
