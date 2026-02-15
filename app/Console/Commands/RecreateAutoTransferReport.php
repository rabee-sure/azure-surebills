<?php

namespace App\Console\Commands;

use App\Exports\TransactionsExport;
use App\Http\Resources\TransactionExportResource;
use App\Models\AutoTransfer;
use App\Models\AutoTransferTransfer;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;

class RecreateAutoTransferReport extends Command
{
    protected $signature = 'transfer_report:recreate {auto_transfer_id}';
    protected $description = 'Recreate auto transfer report files after automatic transfer';

    private $today, $uniqId, $folder;

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
        ini_set('memory_limit','2048M');

        $autoTransfer = AutoTransfer::find($this->argument('auto_transfer_id'));

        if (!$autoTransfer) {
            $this->error("Auto transfer not found.");
            return;
        }

        $transfer_ids = $autoTransfer->tranfer_ids;
        $cycleDate    = Carbon::now()->startOfDay();

        if (count($transfer_ids) > 0) {

            $this->createMasterSheet($transfer_ids, $cycleDate);

            $this->call("transfers:summary", [
                'id' => $transfer_ids,
                'auto_transfer_id' => $autoTransfer->id
            ]);

            // check files on OCI
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

            foreach ($transfer_ids as $transferId) {
                AutoTransferTransfer::create([
                    'auto_transfer_id' => $autoTransfer->id,
                    'transfer_id'      => $transferId,
                ]);
            }
        }

        $this->info("Report recreated successfully.");
    }

    public function createMasterSheet($transfer_ids, $day)
    {
        if (count($transfer_ids) > 0) {

            $this->createMerchantsFile($transfer_ids);
            $this->createChannelsFile($transfer_ids);

            $this->zipFolder($this->folder, "master_sheet_".$this->today.".zip");
        }

        return true;
    }

    public function createMerchantsFile($transfer_ids)
    {
        $transactions = Transaction::whereHas('transfers', function($q) use ($transfer_ids){
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
        $channel_transactions = Transaction::whereHas('transfers', function($q) use ($transfer_ids){
                $q->whereIn('transfer_id', $transfer_ids)
                  ->where(function ($q){
                      $q->whereIn('transaction_source', $this->CHANNEL_SOURCES)
                        ->orWhere('description', 'like', "%Channel:%");
                  });
            })
            ->with('bill.application.channel')
            ->get();

        if (count($channel_transactions) > 0) {

            $filePath = $this->folder."/channels_transactions.xlsx";

            $data = json_decode(
                (TransactionExportResource::collection($channel_transactions))->toJson(),
                true
            );

            Excel::store(
                new TransactionsExport($data, 'channels_transactions'),
                $filePath,
                'oci'
            );
        }
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

        // upload zip to OCI
        Storage::disk('oci')->put(
            $folder_name.'/'.$file_name,
            fopen($tempZipPath, 'r')
        );

        unlink($tempZipPath);
    }
}
