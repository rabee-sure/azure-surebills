<?php

namespace App\Console\Commands;

use App\Exports\MerchantTransactionsExport;
use App\Jobs\MerchantTransactionsExportJob;
use Illuminate\Console\Command;

class ExportMerchantTransactionsManual extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactions:export {user_id} {--from=} {--to=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for export huge transactions for merchant';

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
        ini_set('memory_limit','4096M');
        $user_id = $this->argument('user_id');
        $from = $this->option('from');
        $to = $this->option('to');

        $from = $from ? date('Y-m-d', strtotime($from)) : null;
        $to = $to ? date('Y-m-d', strtotime($to)) : null;

        $file_name = 'Merchant_'.$user_id.'_transactions_from_'.$from.'_to_'.$to.'.xlsx';
        return (new MerchantTransactionsExport($user_id, $from, $to))->store($filePath = 'Manual_Exportations/'. $file_name);
    }
}
