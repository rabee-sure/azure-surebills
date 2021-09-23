<?php

namespace App\Services;

use App\Exports\TransactionsExport;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\TransferLog;
use App\Services\TransferOperations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

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
                'status' => $status,
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

            Transaction::
                where('user_id', $data['user_id'])
                ->amountByCycleDate($data['cycle_date']->format('Y-m-d'))
                ->chunk(1000, function($transactions_ids) use($transfer){
                    $transfer->transactions()->attach($transactions_ids->pluck('id'));
                });

            if($status == 'completed'){
                Transaction::
                    where('user_id', $data['user_id'])
                    ->amountByCycleDate($data['cycle_date']->format('Y-m-d'))
                    ->update(['settled' => true]);
                    TransferService::createTransferTransaction($transfer);

            }elseif($status == 'pending'){
                Transaction::
                    where('user_id', $data['user_id'])
                    ->amountByCycleDate($data['cycle_date']->format('Y-m-d'))
                    ->update(['pending_settled' => true]);
            }elseif($status == 'send_to_sps'){
                $perations = new TransferOperations();
                $perations->sendToSps([$transfer], $status, auth()->user()->id, null, false);
            }

            self::createTransactionsExcel($transfer);

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
        }
    }

    /**
     * create Transfer Transaction.
     *
     * @param  App\Models\Transfer  $transfer
     * @return void
     */
    public static function createTransferTransaction($transfer)
    {
        $bankCode   = $transfer->user->bank ? $transfer->user->bank->code : '-';
        $bankNumber = substr($transfer->user->iban_number, -4);

        $transaction = new Transaction;
        $transaction->user_id     = $transfer->user_id;
        $transaction->type        = 'debit';
        $transaction->amount      = $transfer->amount;
        $transaction->reference   = $transfer->id;
        $transaction->description = 'Transfer - ' . $bankCode . ' XXXX' . $bankNumber;
        $transaction->transaction_source = 'transfer';
        $transaction->save();    
    }


    /**
     * create Transactions Excel.
     *
     * @param  App\Transfer  $transfer
     * @return App\Transfer  $transfer
     */
    public static function createTransactionsExcel($transfer)
    {
        $file_name = self::saveExcelFileName($transfer);
        $data = json_decode((TransactionResource::collection($transfer->transactions))->toJson(), true);
        if(Excel::store(new TransactionsExport($data), $file_name , 'public')){

            $transfer->addMedia(storage_path('app/public/'.$file_name))
                ->preservingOriginal()
                ->toMediaCollection('transfers_transactions');
        }
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
        $fileName = "transfers/{$transfer->user_id}/{$transfer->id}-transfer-transactions-{$cycleDate->format('Y-m-d')}.xlsx";
        $filters = $transfer->filters;
        $filters['files'] = [
            "folder" => explode('/', $fileName)[1],
            "file_name" => explode('/', $fileName)[2],
            "file_path" => $fileName,
        ];
        $transfer->filters = $filters;
        $transfer->save();
        return $fileName;
    }



}
