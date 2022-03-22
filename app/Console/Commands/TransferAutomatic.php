<?php

namespace App\Console\Commands;

use App\Events\TransferCreated;
use App\Exports\BillsExport;
use App\Exports\TransactionsExport;
use App\Exports\TransactionsExportQueued;
use App\Http\Resources\BillResource;
use App\Http\Resources\TransactionExportResource;
use App\Jobs\ExportTransactionsFileJob;
use App\Jobs\SendAutoTransferMailsJob;
use App\Jobs\ZipFolderJob;
use App\Mail\AutoTransferMail;
use App\Models\AutoTransfer;
use App\Models\AutoTransferTransfer;
use App\Models\Bill;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\User;
use App\Services\TransferService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Valuestore\Valuestore;
use ZipArchive;

class TransferAutomatic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:automatic';
    private $today;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'transfer automatic';

    protected $CHANNEL_SOURCES = [
        'channel_extra_amount',
        'channel_extra_amount_vat',
        'channel_extra_amount_fees',
        'channel_fees',
        'channel_vat',
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->today = date('Y-m-d');
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
                    if (!$user->bank) {
                        dd($user->id);
                    }
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
            if(count($transfer_ids) > 0)
            {
                $autoTransfer = AutoTransfer::create([
                    'day' => $this->today,
                    'folder' => "automatic_transfers/".$this->today,
                    'zip_file' => "automatic_transfers/".$this->today."/master_sheet_".$this->today.".zip",
                    'merchants_file' => "automatic_transfers/".$this->today."/merchants_transactions.xlsx",
                    'channels_file' => "automatic_transfers/".$this->today."/channels_transactions.xlsx",
                    'tranfer_ids' => $transfer_ids,
                ]);

                $this->call("transfers:summary", ['id' =>  $transfer_ids, 'auto_transfer_id' => $autoTransfer->id]);

                foreach($transfer_ids as $transferId)
                {
                    AutoTransferTransfer::create([
                        'auto_transfer_id' => $autoTransfer->id,
                        'transfer_id' => $transferId,
                    ]);
                }
            }
        }
    }

    /**
     * create Transactions Excel.
     *
     * @param  App\Transfer  $transfer
     * @return App\Transfer  $transfer
     */
    public function createMasterSheet($transfer_ids, $day)
    {
        if(count($transfer_ids)){
            $this->createMerchantsFile($transfer_ids, $day);
            $this->createChannelsFile($transfer_ids, $day);
            $this->zipFolder("automatic_transfers/".$this->today, "master_sheet_".$this->today.".zip");
            $this->sendMails($day);
        }
        return true;
    }

    public function createMerchantsFile($transfer_ids, $day)
    {
        $transactions = Transaction::whereHas('transfers', function($q) use($transfer_ids){
                $q->whereIn('transfer_id', $transfer_ids)
                    ->whereNotIn('transaction_source', $this->CHANNEL_SOURCES)
                    ->where('description', 'not like', "%Channel:%");
            })->with('bill.application.channel')->get();

        $merchants_file = "automatic_transfers/".$this->today."/merchants_transactions.xlsx";
        $data = json_decode((TransactionExportResource::collection($transactions))->toJson(), true);
        Excel::store(new TransactionsExport($data), $merchants_file , 'public');
    }

    public function createChannelsFile($transfer_ids, $day)
    {
        $channels_file = "automatic_transfers/".$this->today."/channels_transactions.xlsx";

        $channel_transactions = Transaction::whereHas('transfers', function($q) use($transfer_ids){
            $q->whereIn('transfer_id', $transfer_ids)
                ->where(function ($q){
                    $q->whereIn('transaction_source', $this->CHANNEL_SOURCES)
                        ->orWhere('description', 'like', "%Channel:%")
                        ;
                });
        })->with('bill.application.channel')->get();
        $channels_data = json_decode((TransactionExportResource::collection($channel_transactions))->toJson(), true);
        Excel::store(new TransactionsExport($channels_data), $channels_file , 'public');
    }

    public function zipFolder($folder_name , $file_name)
    {
        $file_full_path = 'app/public/'.$folder_name.'/'.$file_name;
        //first delete file
        if(is_file(storage_path($file_full_path)))
            unlink(storage_path($file_full_path));

        $zip = new ZipArchive();

        if ($zip->open(storage_path($file_full_path), ZipArchive::CREATE) === TRUE){
            $files = File::files(storage_path("app/public/$folder_name"));
            foreach ($files as $key => $value) {
                $relativeNameInZipFile = basename($value);
                $zip->addFile($value, $relativeNameInZipFile);
            }
            $zip->close();
        }
    }

    public function sendMails($day)
    {
        $settings =  Valuestore::make(storage_path('app/settings.json'));
        $transfer_emails = $settings->get('transfer_emails');
        $emails = explode(",", $transfer_emails);
        if(count($emails)){
            foreach ($emails as $email) {
                Mail::to($email)->send(new AutoTransferMail($day));
            }
        }
    }

}
