<?php

namespace App\Services;

use App\Exports\TransactionsExportQueued;
use App\Http\Resources\TransactionExportResource;
use App\Jobs\CreateTransferExcelFileJob;
use App\Jobs\UpdateTransferExcelFile;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\TransferLog;
use App\Models\User;
use App\Support\Storage\ExportStoragePaths;
use App\Services\BasicSettingsService;
use App\Services\TransferOperations;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
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
        $transfer = DB::transaction(function () use ($status, $data) {
            $merchant = User::where('id', $data['user_id'])->lockForUpdate()->first();

            if (! $merchant) {
                throw ValidationException::withMessages([
                    'amount' => [__('amount must be greater than 0')],
                ]);
            }

            $cycleDate = $data['cycle_date'] instanceof Carbon
                ? $data['cycle_date']->format('Y-m-d')
                : Carbon::parse($data['cycle_date'])->format('Y-m-d');

            $unsettled = Transaction::select('id', 'type', 'amount')
                ->where('user_id', $merchant->id)
                ->amountByCycleDate($cycleDate)
                ->get();

            $transactionIds = $unsettled->pluck('id')->all();
            $lockedAmount = floorp(
                $unsettled->where('type', 'credit')->sum('amount') - $unsettled->where('type', 'debit')->sum('amount'),
                2
            );

            $settings = app(BasicSettingsService::class)->getSettings();
            $transfer_minimum = (float) ($settings['transfer_minimum'] ?? 0);

            if ($transactionIds === [] || $lockedAmount < $transfer_minimum) {
                throw ValidationException::withMessages([
                    'amount' => [__('Your balance is not allowed to transfer. The minimum transfer balance is :minimum', ['minimum' => $transfer_minimum])],
                ]);
            }

            $transfer = Transfer::create([
                'status' => 'pending',
                'amount' => $lockedAmount,
                'user_id' => $merchant->id,
                'transfer_fees' => $data['transfer_fees'],
                'net_amount' => $lockedAmount - $data['transfer_fees'],
                'note' => $data['note'] ?? null,
                'attachment' => $data['attachment'] ?? null,
                'created_by_id' => $data['created_by_id'],
                'bank_id' => $data['bank_id'],
                'iban_number' => $data['iban_number'],
                'beneficiary_name' => $data['beneficiary_name'],
                'filters' => [
                    'date' => [
                        'cycle_date' => $data['cycle_date'],
                    ],
                ],
            ]);

            TransferLog::create([
                'type' => 'create transfer',
                'user_id' => auth()->user()->id ?? null,
                'transfer_id' => $transfer->id,
                'transfer_status' => $status,
            ]);

            foreach (array_chunk($transactionIds, 1000) as $chunk) {
                $transfer->transactions()->attach($chunk);
            }

            $perations = new TransferOperations();
            if ($status == 'completed') {
                $perations->complete([$transfer], $status, auth()->user()->id ?? null);
            } elseif ($status == 'pending') {
                $user_id = null;
                if (auth()->user()) {
                    if (auth()->user()->id) {
                        if (Auth::guard('admins')->check()) {
                            $user_id = auth()->user()->id;
                        }
                    }
                }
                $perations->pending([$transfer], $status, $user_id);
            } elseif ($status == 'send_to_sps') {
                $perations->sendToSps([$transfer], $status, auth()->user()->id ?? null, null, false, false);
            }

            return $transfer->fresh();
        });

        self::saveExcelFileName($transfer);
        CreateTransferExcelFileJob::dispatch($transfer->id);

        if ($status == 'send_to_sps' && $transfer->status == 'send_to_sps') {
            (new TransferOperations())->postToSps([$transfer]);
        }

        return $transfer;
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
