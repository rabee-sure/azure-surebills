<?php

namespace App\Console\Commands;

use App\Events\TransferCreated;
use App\Exports\BillsExport;
use App\Exports\TransactionsExport;
use App\Exports\TransactionsExportQueued;
use App\Http\Resources\BillResource;
use App\Http\Resources\TransactionExportResource;
use App\Models\AutoTransfer;
use App\Models\AutoTransferTransfer;
use App\Models\Bill;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\User;
use App\Services\BasicSettingsService;
use App\Services\TransferService;
use App\Support\Storage\ExportStoragePaths;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;

class TransferAutomatic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:automatic';
    private $today, $uniqId, $folder;

    private array $working_days = [
        'sun' => 0,
        'mon' => 1,
        'tue' => 2,
        'wed' => 3,
        'thr' => 4,
    ];

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
        $this->uniqId = uniqid();
        $this->folder = ExportStoragePaths::automaticTransferFolder($this->today, $this->uniqId);
    }


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(BasicSettingsService $basicSettingsService)
    {
        ini_set('memory_limit','3072M');
<<<<<<< HEAD
        $settings = $basicSettingsService->getSettings();
        $transfer_automatic = $settings['transfer_automatic'] ?? null;
=======
        $settings =  Valuestore::make(getSettings());
        $transfer_automatic = $settings->get('transfer_automatic');
        $transfer_days = [];
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4

        $transfer_days = collect($this->working_days)->map(function($number, $day) use ($settings){
            if(!empty($settings[$day])){
                return $number;
            }
        })->filter(fn($day) => $day !== null)->toArray();

<<<<<<< HEAD
        $transfer_minimum = (float) ($settings['transfer_minimum'] ?? 0);
        $transfer_emails = $settings['transfer_emails'] ?? '';
=======
        $transfer_minimum = $settings->get('transfer_minimum');
        $transfer_emails = $settings->get('transfer_emails');
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4

        $cycleDate = Carbon::now()->startOfDay();
        if($transfer_automatic && in_array($cycleDate->dayOfWeek, array_values($transfer_days)) ){
            $users = User::where('verified', true)->where('auto_trnasfer', true)->with('bank')->get();

            $filtered_users = $users->filter(function($user) use($transfer_minimum){
                return $user->actual_balance >= $transfer_minimum;
            });
            $transfer_ids = [];
            foreach ($filtered_users as $user) {
                $amount = $user->getBalanceBefore($cycleDate->format('Y-m-d'));
                $this->info("user ID: $user->id, amount: $amount");
                if($amount  >= $transfer_minimum){
                    $this->info("transfer to user ID $user->id amount: $amount");

                    if($user->bank_id == null){
                        $this->info("user ID $user->id has no bank");
                        continue;
                    }

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

            if(count($transfer_ids) > 0)
            {
                $autoTransfer = AutoTransfer::create([
                    'day' => $this->today,
                    'folder' => $this->folder,
                    'tranfer_ids' => $transfer_ids,
                ]);

                $this->createMasterSheet($transfer_ids, $cycleDate, $transfer_emails);
                $this->call("transfers:summary", ['id' =>  $transfer_ids, 'auto_transfer_id' => $autoTransfer->id]);

                $autoTransfer->zip_file = Storage::exists($this->folder."/master_sheet_".$this->today.".zip") ? $this->folder."/master_sheet_".$this->today.".zip" : null;
                $autoTransfer->merchants_file = Storage::exists($this->folder."/merchants_transactions.xlsx") ? $this->folder."/merchants_transactions.xlsx" : null;
                $autoTransfer->channels_file = Storage::exists($this->folder."/channels_transactions.xlsx") ? $this->folder."/channels_transactions.xlsx" : null;
                $autoTransfer->save();

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
    public function createMasterSheet($transfer_ids, $day, $transfer_emails = '')
    {
        if(count($transfer_ids) > 0){
            $this->createMerchantsFile($transfer_ids, $day);
            $this->createChannelsFile($transfer_ids, $day);
            $this->zipFolder($this->folder, "master_sheet_".$this->today.".zip");
            $this->sendMails($day, $transfer_emails);
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

        $merchants_file = $this->folder."/merchants_transactions.xlsx";
        $data = json_decode((TransactionExportResource::collection($transactions))->toJson(), true);
        Excel::store(new TransactionsExport($data, 'merchants_transactions'), $merchants_file);
    }

    public function createChannelsFile($transfer_ids, $day)
    {
        $channels_file = $this->folder."/channels_transactions.xlsx";
        $channel_transactions = Transaction::whereHas('transfers', function($q) use($transfer_ids){
            $q->whereIn('transfer_id', $transfer_ids)
                ->where(function ($q){
                    $q->whereIn('transaction_source', $this->CHANNEL_SOURCES)
                        ->orWhere('description', 'like', "%Channel:%")
                        ;
                });
        })->with('bill.application.channel')->get();

        if(count($channel_transactions) > 0)
        {
            $channels_data = json_decode((TransactionExportResource::collection($channel_transactions))->toJson(), true);
            Excel::store(new TransactionsExport($channels_data, 'channels_transactions'), $channels_file);
        }
    }

    public function zipFolder($folder_name, $file_name)
    {
        $disk = Storage::disk('public');
        $folder = trim($folder_name, '/');
        $relativeZip = $folder.'/'.$file_name;

        if ($disk->exists($relativeZip)) {
            $disk->delete($relativeZip);
        }

        $tempLocal = storage_path('app/temp-zip-'.uniqid('', true).'-'.$file_name);
        @mkdir(dirname($tempLocal), 0755, true);

        $zip = new ZipArchive;
        if ($zip->open($tempLocal, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return;
        }

        foreach ($disk->files($folder) as $path) {
            if ($path === $relativeZip) {
                continue;
            }
            $zip->addFromString(basename($path), $disk->get($path));
        }
        $zip->close();

        if (is_file($tempLocal)) {
            $stream = fopen($tempLocal, 'r');
            if ($stream !== false) {
                $disk->writeStream($relativeZip, $stream);
                if (is_resource($stream)) {
                    fclose($stream);
                }
            }
            @unlink($tempLocal);
        }
    }

    public function sendMails($day, $transfer_emails = '')
    {
<<<<<<< HEAD
        $emails = array_values(array_filter(array_map('trim', explode(',', $transfer_emails ?? ''))));

        foreach ($emails as $email) {
            // Mail::to($email)->send(new AutoTransferMail($day));
=======
        $settings =  Valuestore::make(getSettings());
        $transfer_emails = $settings->get('transfer_emails');
        $emails = explode(",", $transfer_emails);
        if(count($emails)){
            foreach ($emails as $email) {
                // Mail::to($email)->send(new AutoTransferMail($day));
            }
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4
        }
    }

}
