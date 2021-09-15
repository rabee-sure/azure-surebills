<?php

namespace App\Services;

use App\Events\TransferCreated;
use App\Exports\BillsExport;
use App\Exports\TransactionsExport;
use App\Http\Resources\BillResource;
use App\Http\Resources\TransactionResource;
use App\Mail\AutoTransferMail;
use App\Models\Bill;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\TransferLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class TransferService 
{
    /**
     * get bills Between Date.
     *
     * @return \Illuminate\Http\Response
     */
    public static function getTransactionsByCycleDate($cycleDate, $user, $excel_file_name = null)
    {
        $cycleDate = $cycleDate->copy()->endOfDay()->toDateTimeString();

        $transactions = Transaction::where('user_id', $user->id)
            ->where('settled', false)
            ->where('pending_settled', false)
            ->where('transaction_source', '!=', "transfer")
            ->whereDate('created_at', '<=', $cycleDate)
            ->orderBy('created_at', 'ASC')
            ->orderBy('order', 'ASC')
            ->orderBy('receipt', 'ASC')
            ->get();

        if($excel_file_name){
            self::createTransactionsExcel($transactions, $excel_file_name);
        }

        return $transactions;
    }


    /**
     * get Amount.
     *
     * @param  App\Bill  $bills
     * @param  App\User  $user
     * @return double
     */
    public static function getAmount($transactions)
    {
        return floorp(
            $transactions->where('type', 'credit')->sum('amount') -
            $transactions->where('type', 'debit')->sum('amount')
            , 2);
    }  

    /**
     * create Transactions Excel.
     *
     * @param  App\Bill  $bills
     * @param  string  $file_name
     * @return boolean
     */
    public static function createTransactionsExcel($transactions, $file_name)
    {
        $array = explode('/', $file_name);
        $array[2] = 'transactions-'.$array[2];
        $file_name = implode('/', $array);
        $data = json_decode((TransactionResource::collection($transactions))->toJson(), true);
        return Excel::store(new TransactionsExport($data), $file_name);
    }

    /**
     * Make Transaction.
     *
     * @param  String  $status
     * @param  double  $amount
     * @param  Collection  $bills
     * @param  Array  $data
     *
     * @return void
     */
    public static function makeTransfer($status, $amount, $transactions, $data)
    {
        return DB::transaction(function () use($status, $amount, $transactions, $data){
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
                    'files' => [
                        "folder" => explode('/', $data['file_name'])[1],
                        "transactions" => 'transactions-'.explode('/', $data['file_name'])[2],
                    ],
                ],
            ]);

            $log = TransferLog::create([
                'type' => 'create transfer',
                'user_id' => auth()->user()->id?? null,
                'transfer_id' => $transfer->id,
                'transfer_status' => $status,
            ]);

            foreach ($transactions as $transaction) {
                if($status == 'completed'){
                    if($transaction->user_id == $data['user_id']){
                        $transaction->settled = true;
                    }
                }else{
                    $transaction->pending_settled = true;
                }
                $transaction->save();
            }

            $transfer->transactions()->attach($transactions->pluck('id')->toArray());

            if($status == 'completed'){
                TransferService::createTransferTransaction($transfer);
            }elseif($status == 'send_to_sps'){
                $this->sendToSPS($transfer, $log);
            }

            return $transfer;
        }); 
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
     * send To SPS.
     *
     * @param  App\Models\Transfer  $transfer
     * @return void
     */
    protected function sendToSPS($transfer, $log)
    {
        $body = [
            'ReferenceNumber' => $transfer->id,
            'Amount' => $transfer->amount,
            'BeneficiaryName' => $transfer->user->beneficiary_name,
            'BeneficiaryIban' => $transfer->user->iban_number,
            'BeneficiaryStreet' => $transfer->user->business_address,
            'BeneficiaryCountry' => 'SA',
            'BeneficiaryBank' => $transfer->user->bank->code,
        ];
        //here request to transfer/sps
    }


    /**
     * change Tranfer Status Transaction.
     * 
     * @param  App\Models\Transfer  $transfer
     * @return void
     */
    public static function changeTranferStatus($transfer, $status, $user_id=null, $results=null , $from_sps=false)
    {
        if($transfer->status == 'pending' ){
            $transfer->status = $status;
            $transfer->save();

             $log = TransferLog::create([
                'type' =>  $from_sps ? $status.' sps transfer':$status.' transfer' ,
                'user_id' => $user_id,
                'transfer_id' => $transfer->id,
                'transfer_status' => $transfer->status,
                'status' => $status,
                'results' => $results,
            ]);

            if($status == 'completed'){
                
                TransferService::createTransferTransaction($transfer);

                $bills = $transfer->bills;
                $user_id = $transfer->user_id;
                foreach ($bills as $bill) {
                    if($bill->user_id == $user_id){
                        $bill->settled = true;
                    }

                    if($bill->isHaveChannelOwenByUser($user_id)){
                       $bill->channel_settled = true; 
                    }
                    $bill->save();
                }
                event(new TransferCreated($transfer));
            }elseif($status == 'canceled'){
                $bills = $transfer->bills;
                foreach ($bills as $bill) {
                    $bill->pending_settled = false; 
                    $bill->save();
                }

                $transactions = $transfer->transactions;
                foreach ($transactions as $transaction) {
                    $transaction->pending_settled = false; 
                    $transaction->save();
                }
            }
        }
    }

}
