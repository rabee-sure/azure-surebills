<?php

namespace App\Services;

use App\Exports\TransactionsExportQueued;
use App\Http\Resources\TransactionExportResource;
use App\Jobs\CreateTransferExcelFileJob;
use App\Jobs\UpdateTransferExcelFile;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\TransferLog;
use App\Support\Storage\ExportStoragePaths;
use App\Services\TransferOperations;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class TransferService 
{ 
    /**
     * Make Transfer.
     *
     * @param  String  $status
     * @param  double  $amount
     * @param  Array  $data
     *
     * @return void
     */
    public static function makeTransfer($status, $amount, $data)
    {
        return DB::transaction(function () use($status, $amount, $data){

            $transfer = Transfer::create([
                'status' => 'pending',
                'amount' => $amount,
                'user_id' => $data['user_id'],
                'transfer_fees' => $data['transfer_fees'],
                'net_amount' => $amount - $data['transfer_fees'],
                'note' => $data['note'] ?? null,
                'attachment' => $data['attachment'] ?? null,
                'created_by_id' => $data['created_by_id'],
                'bank_id' => $data['bank_id'],
                'iban_number' => $data['iban_number'],
                'beneficiary_name' => $data['beneficiary_name'],
                'filters' => [
                    'date' => [
                        "cycle_date" => $data['cycle_date'],
                    ],
                ],
            ]);


            $log = TransferLog::create([
                'type' => 'create transfer',
                'user_id' => auth()->user()->id?? null,
                'transfer_id' => $transfer->id,
                'transfer_status' => $status,
            ]);

            Transaction::select('id')
                ->where('user_id', $data['user_id'])
                ->amountByCycleDate($data['cycle_date']->format('Y-m-d'))
                ->chunk(1000, function($transactions_ids) use($transfer){
                    $transfer->transactions()->attach($transactions_ids->pluck('id'));
                });

            $perations = new TransferOperations();
            if($status == 'completed'){
                $perations->complete([$transfer], $status, auth()->user()->id);
            }elseif($status == 'pending'){
                $user_id = null;
                if(auth()->user()){
                    if(auth()->user()->id){
                        if (Auth::guard('admins')->check()) {
                            $user_id = auth()->user()->id;
                        }
                    }
                }
                $perations->pending([$transfer], $status, $user_id);
            }elseif($status == 'send_to_sps'){
                $perations->sendToSps([$transfer], $status, auth()->user()->id);
            }

            $file_name = self::saveExcelFileName($transfer);

            CreateTransferExcelFileJob::dispatch($transfer->id);

            return $transfer;
        }); 
    }


    public static function changeTranfersStatus($transfers, $status, $user_id=null, $results=null , $from_sps=false)
    {
        $perations = new TransferOperations();
        if($status == 'send_to_sps'){
            $perations->sendToSps($transfers, $status, $user_id, $results , $from_sps);
        }else if($status == 'completed'){
            $perations->complete($transfers, $status, $user_id, $results , $from_sps);
        }elseif($status == 'canceled'){
            $perations->cancel($transfers, $status, $user_id, $results , $from_sps);
        }elseif($status == 'failed'){
            $perations->fail($transfers, $status, $user_id, $results , $from_sps);
        }
    }


    /**
     * create Transactions Excel.
     *
     * @param  App\Transfer  $transfer
     * @return App\Transfer  $transfer
     */
    public static function createTransactionsExcel($transfer, $file_name)
    {
        $data = json_decode((TransactionExportResource::collection($transfer->transactions->load('bill.application.channel')))->toJson(), true);
        
        (new TransactionsExportQueued($data))->store($file_name, 'public')->chain([
            new UpdateTransferExcelFile($transfer, $file_name),
        ]);
        

        // if(Excel::store(new TransactionsExportQueued($data), $file_name , 'public')){

        //     $transfer->addMedia(storage_path('app/public/'.$file_name))
        //         ->preservingOriginal()
        //         ->toMediaCollection('transfers_transactions');
        // }
        return $transfer;
    }

    /**
     * save Excel File Name.
     *
     * @return String
     */
    public static function saveExcelFileName($transfer)
    {
        $date = $transfer->filters['date']['cycle_date'] ?? $transfer->filters['date']['from'];
        $cycleDate = Carbon::parse($date);
        $workbookFileName = "{$transfer->id}-transfer-transactions-{$cycleDate->format('Y-m-d')}.xlsx";
        $fileName = ExportStoragePaths::transferWorkbook((int) $transfer->user_id, $workbookFileName);
        $filters = $transfer->filters;
        $filters['files'] = [
            'folder' => dirname($fileName),
            'file_name' => basename($fileName),
            'file_path' => $fileName,
        ];
        $transfer->filters = $filters;
        $transfer->save();
        return $fileName;
    }



}
