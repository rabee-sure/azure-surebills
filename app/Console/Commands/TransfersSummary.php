<?php

namespace App\Console\Commands;

use App\Exports\DueAmountsExport;
use App\Exports\MerchantsSummaryExport;
use App\Exports\TransactionsExport;
use App\Http\Resources\TransactionExportResource;
use App\Models\Application;
use App\Models\Transaction;
use App\Models\Transfer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Mail;
use App\Mail\TransfersSummaryMail;
use App\Models\AutoTransfer;
use App\Services\BasicSettingsService;
use App\Support\Storage\ExportStoragePaths;

class TransfersSummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfers:summary {auto_transfer_id} {id?*  : The IDs of the transfers}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create transfer summary excel sheet.';


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
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(BasicSettingsService $basicSettingsService)
    {
        $ids = (count($this->argument('id')) == 1) ? explode(',', $this->argument('id')[0]):$this->argument('id');
        $transfers = Transfer::whereIn('id', $ids)->with('user.bank', 'transactions')->get();

        $transfer_ids = $transfers->pluck('id')->toArray();
        $transactions = Transaction::whereHas('transfers', function($q) use($transfer_ids){
                $q->whereIn('transfer_id', $transfer_ids)->whereNotIn('transaction_source', $this->CHANNEL_SOURCES);
            })->with('bill.application.channel')->get();

        $merchantsSummaryFile = $this->createMerchantsSummaryFile($transfers, $transactions);
        $dueAmountFile = $this->createDueAmountsFile($transfers, $transactions);
        $this->sendMails($transfers, $basicSettingsService->get('transfer_emails', ''));

        $autoTransfer = AutoTransfer::find($this->argument('auto_transfer_id'));
        $autoTransfer->due_amount_file = $dueAmountFile;
        $autoTransfer->merchants_summary_file = $merchantsSummaryFile;
        $autoTransfer->save();
    }


    public function createMerchantsSummaryFile($transfers, $transactions)
    {
        $data = $this->getMerchantsSummaryData($transfers, $transactions);

        $t_file_n = $this->getFileName($transfers);
        $file_name = ExportStoragePaths::summaryTransferFolder($t_file_n).'/merchants_summary.xlsx';

        Excel::store(new MerchantsSummaryExport($data), $file_name , 'public');

        return $file_name;
    }


    public function getMerchantsSummaryData($transfers, $transactions)
    {
        $data = [];
        foreach($transfers as $transfer){
            $this->info("Merchants Summary transfer id:{$transfer->id}");
            $user = $transfer->user;
            $user_transactions =  $transactions->where('user_id', $user->id);
            $bills = $transactions->pluck('bill')->unique()->where('user_id', $user->id);
            $data[] = $this->getSummaryItem($user, $user_transactions, $bills, 'MADA');
            $data[] = $this->getSummaryItem($user, $user_transactions, $bills, 'VISA');
            $data[] = $this->getSummaryItem($user, $user_transactions, $bills, 'MASTERCARD');
        }

        return $data;
    }

    public function getSummaryItem($user, $transactions, $bills, $card_brand)
    {
        $bills = $bills->where('paymentMethodDetails',$card_brand);
        $pricing = $bills->whereNotNull('pricing')->first()->pricing ?? [];
        return [
            'client_id' => $user->id,
            'payment_type' => $card_brand,
            'no_of_trx' => $bills->count(),
            'total_amount' => $bills->sum('total'),
            'total_fees' => $bills->sum('payment_fees'),
            'total_fees_vat' => $bills->sum('payment_fees_vat'),
            'total_fees_variable_rate' => $pricing['fees_percentage'] ?? '',
            'total_fees_fixed_rate' => $pricing['fees_fixed'] ?? '',
            'sure_variable_rate' => $pricing['surebills_fees_percentage'] ?? '',
            'sure_fixed_rate' => $pricing['surebills_fees_fixed'] ?? '',
            'channel_variable_rate' => $pricing['channel_fees_percentage'] ?? '',
            'channel_fixed_rate' => $pricing['channel_fees_fixed'] ?? '',
            'sure_fees' => $bills->sum('payment_surebills_fees'),
            'sure_vat' => $bills->sum('payment_surebills_fees_vat'),
            'channel_fees' => $bills->sum('payment_channel_fees'),
            'channels_vat' => $bills->sum('payment_channel_fees_vat'),
            'channel_id' => $this->getChannelId($bills),
        ];
    }

    public function createDueAmountsFile($transfers, $transactions)
    {
        $data = $this->getDueAmountsData($transfers, $transactions);

        $t_file_n = $this->getFileName($transfers);
        $file_name = ExportStoragePaths::summaryTransferFolder($t_file_n).'/due_amounts.xlsx';

        Excel::store(new DueAmountsExport($data), $file_name , 'public');

        return $file_name;
    }

    public function getDueAmountsData($transfers, $transactions)
    {
        $data = [];
        foreach($transfers as $transfer){
            $user = $transfer->user;
            $trans = $transactions->where('user_id', $user->id);
            $this->info("Due Amounts transfer id: {$transfer->id} count: {$trans->count()}");
            $data[] = $this->getDueAmountsItem($transfer, $user, $trans);
        }
        return $data;
    }

    public function getDueAmountsItem($transfer, $user, $transactions)
    {
        $bills = $transactions->pluck('bill')->unique()->where('user_id', $user->id);
        $trans = $transfer->transactions;
        $sum_bills = $trans->where('transaction_source', 'bill')->sum('amount') + $trans->where('transaction_source', 'channel_fees')->sum('amount') + $trans->where('transaction_source', 'channel_vat')->sum('amount');
        $sum_fees = $trans->where('transaction_source', 'fees')->sum('amount');
        $sum_fees_vat = $trans->where('transaction_source', 'vat')->sum('amount');
        $sum_refund = $trans->where('transaction_source', 'refund')->where('type', 'debit')->sum('amount') - $trans->where('transaction_source', 'refund')->where('type', 'credit')->sum('amount');

        return [
            'merchant_id' => $user->id,
            'merchant_name' => $user->business_name_ar,
            'merchan_iban' => $user->iban_number,
            'bank' => $user->bank->name,
            'total_amount' => $sum_bills,
            'total_fees' => $sum_fees,
            'total_fees_vat' => $sum_fees_vat,
            'total_refund' => $sum_refund,
            // 'sure_fees' => $bills->sum('payment_surebills_fees'),
            // 'sure_fees_vat' => $bills->sum('payment_surebills_fees_vat'),
            // 'channel_fees' => $bills->sum('payment_channel_fees'),
            // 'channel_fees_vat' => $bills->sum('payment_channel_fees_vat'),
            'bank_charges' => $transfer->transfer_fees,
            'net_due' => $transfer->net_amount,
            'channel_id' => $this->getChannelId($bills),
            'transfer_id' => $transfer->id,
        ];
    }

    public function sendMails($transfers, $transfer_emails = '')
    {
        $emails = array_values(array_filter(array_map('trim', explode(',', $transfer_emails ?? ''))));
        $t_file_n = $this->getFileName($transfers);

        foreach ($emails as $email) {
            Mail::to($email)->queue(new TransfersSummaryMail($t_file_n));
        }
    }

    protected function getChannelId($bills)
    {
        $application_id = $bills->whereNotNull('application_id')->first()->application_id ?? null;
        return $application_id ? Application::find($application_id)->channel_id ?? '' : '';
    }

    /**
     * @param $transfers
     * @return string
     */
    public function getFileName($transfers): string
    {
        return 'number of transfers (' . $transfers->count() . ')';
    }
}
