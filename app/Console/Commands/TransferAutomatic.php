<?php

namespace App\Console\Commands;

use App\Exports\TransactionsExport;
use App\Http\Resources\TransactionExportResource;
use App\Models\AutoTransfer;
use App\Models\AutoTransferTransfer;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransferService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Valuestore\Valuestore;
use ZipArchive;

class TransferAutomatic extends Command
{
    protected $signature = 'transfer:automatic';
    protected $description = 'Automatic transfer process';

    private $today, $uniqId, $folder;

    private array $working_days = [
        'sun' => 0,
        'mon' => 1,
        'tue' => 2,
        'wed' => 3,
        'thr' => 4,
    ];

    protected $CHANNEL_SOURCES = [
        'channel_extra_amount',
        'channel_extra_amount_vat',
        'channel_extra_amount_fees',
        'channel_fees',
        'channel_vat',
    ];

    public function __construct()
    {
        parent::__construct();

        $this->today  = date('Y-m-d');
        $this->uniqId = uniqid();
        $this->folder = "automatic_transfers/".$this->today."/".$this->uniqId;
    }

    public function handle()
    {
        ini_set('memory_limit','3072M');

        $settings = Valuestore::make(getSettings());
        $transfer_automatic = $settings->get('transfer_automatic');
        $transfer_minimum   = $settings->get('transfer_minimum');

        $transfer_days = collect($this->working_days)->map(function($number, $day) use ($settings){
            if($settings->get($day)){
                return $number;
            }
        })->filter()->toArray();

        $cycleDate = Carbon::now()->startOfDay();

        if (!$transfer_automatic ||
            !in_array($cycleDate->dayOfWeek, array_values($transfer_days))) {
            return;
        }

        $users = User::where('verified', true)
            ->where('auto_trnasfer', true)
            ->with('bank')
            ->get();

        $transfer_ids = [];

        foreach ($users as $user) {

            if ($user->bank_id == null) {
                continue;
            }

            $amount = $user->getBalanceBefore($cycleDate->format('Y-m-d'));

            if ($amount < $transfer_minimum) {
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

        if (count($transfer_ids) === 0) {
            return;
        }

        $autoTransfer = AutoTransfer::create([
            'day' => $this->today,
            'folder' => $this->folder,
            'tranfer_ids' => $transfer_ids,
        ]);

        $this->createMasterSheet($transfer_ids);

        $this->call("transfers:summary", [
            'id' => $transfer_ids,
            'auto_transfer_id' => $autoTransfer->id
        ]);

        // Check files on OCI
        $autoTransfer->zip_file = Storage::disk('oci')
            ->exists($this->folder."/master_sheet_".$this->today.".zip")
            ? $this->folder."/master_sheet_".$this->today.".zip"
            : null;

        $autoTransfer->merchants_file = Storage::disk('oci')
            ->exists($this->folder."/merchants_transactions.xlsx")
            ? $this->folder."/merchants_transactions.xlsx"
            : null;

        $autoTransfer->channels_file = Storage::disk('oci')
            ->exists($this->folder."/channels_transactions.xlsx")
            ? $this->folder."/channels_transactions.xlsx"
            : null;

        $autoTransfer->save();

        foreach($transfer_ids as $transferId)
        {
            AutoTransferTransfer::create([
                'auto_transfer_id' => $autoTransfer->id,
                'transfer_id' => $transferId,
            ]);
        }

        $this->sendMails();
    }

    public function createMasterSheet($transfer_ids)
    {
        $this->createMerchantsFile($transfer_ids);
        $this->createChannelsFile($transfer_ids);
        $this->zipFolder($this->folder, "master_sheet_".$this->today.".zip");
    }

    public function createMerchantsFile($transfer_ids)
    {
        $transactions = Transaction::whereHas('transfers', function($q) use($transfer_ids){
                $q->whereIn('transfer_id', $transfer_ids)
                  ->whereNotIn('transaction_source', $this->CHANNEL_SOURCES)
                  ->where('description', 'not like', "%Channel:%");
            })
            ->with('bill.application.channel')
            ->get();

        $filePath = $this->folder."/merchants_transactions.xlsx";

        $data = json_decode(
            (TransactionExportResource::collection($transactions))->toJson(),
            true
        );

        Excel::store(
            new TransactionsExport($data, 'merchants_transactions'),
            $filePath,
            'oci'
        );
    }

    public function createChannelsFile($transfer_ids)
    {
        $transactions = Transaction::whereHas('transfers', function($q) use($transfer_ids){
                $q->whereIn('transfer_id', $transfer_ids)
                  ->where(function ($q){
                      $q->whereIn('transaction_source', $this->CHANNEL_SOURCES)
                        ->orWhere('description', 'like', "%Channel:%");
                  });
            })
            ->with('bill.application.channel')
            ->get();

        if ($transactions->isEmpty()) {
            return;
        }

        $filePath = $this->folder."/channels_transactions.xlsx";

        $data = json_decode(
            (TransactionExportResource::collection($transactions))->toJson(),
            true
        );

        Excel::store(
            new TransactionsExport($data, 'channels_transactions'),
            $filePath,
            'oci'
        );
    }

    public function zipFolder($folder_name , $file_name)
    {
        $tempZipPath = storage_path('app/temp_'.$file_name);

        if (file_exists($tempZipPath)) {
            unlink($tempZipPath);
        }

        $zip = new ZipArchive();

        if ($zip->open($tempZipPath, ZipArchive::CREATE) === TRUE) {

            $files = Storage::disk('oci')->files($folder_name);

            foreach ($files as $file) {

                if (str_contains($file, '.zip')) {
                    continue;
                }

                $stream = Storage::disk('oci')->readStream($file);

                if ($stream) {
                    $contents = stream_get_contents($stream);
                    $zip->addFromString(basename($file), $contents);
                    fclose($stream);
                }
            }

            $zip->close();
        }

        Storage::disk('oci')->put(
            $folder_name.'/'.$file_name,
            fopen($tempZipPath, 'r')
        );

        unlink($tempZipPath);
    }


    public function sendMails($day)
    {
        $settings =  Valuestore::make(getSettings());
        $transfer_emails = $settings->get('transfer_emails');
        $emails = explode(",", $transfer_emails);
        if(count($emails)){
            foreach ($emails as $email) {
                // Mail::to($email)->send(new AutoTransferMail($day));
            }
        }
    }
}
