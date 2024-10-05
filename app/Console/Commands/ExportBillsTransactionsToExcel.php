<?php

namespace App\Console\Commands;

use App\Exports\BillsTransactionsExport;
use App\Models\Bill;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExportBillsTransactionsToExcel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:bills_transactions {--user_id=} {--bills_ids=} {--from=} {--to=} {--period_column=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for export transactions for bills for specefic ids or period';

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
        $options = [
            'user_id' => $this->option('user_id'),
            'bills_ids' => $this->option('bills_ids') ? explode(',', $this->option('bills_ids')) : null,
            'from' => $this->option('from'),
            'to' => $this->option('to'),
            'period_column' => $this->option('period_column'),
        ];

        if (($options['from'] || $options['to']) && !$options['period_column']) {
            $this->error('The --period_column option is mandatory when --from or --to are provided.');
            return 1;
        }

        $billsSummary = Bill::select(DB::raw(
            "SUM(CASE WHEN status  = 'paid' THEN fixed_total ELSE 0 END) AS paid_total,
            SUM(CASE WHEN status  = 'refunded' THEN fixed_total ELSE 0 END) AS refund_total,
            COUNT('id') AS bills_counter"
        ));

        if(!empty($options['bills_ids'])){
            $this->info('bills_ids provided');
            $billsSummary = $billsSummary->whereIn('id', $options['bills_ids']);
        }else{
            $this->info('use other filter provided');
            if($options['user_id'] != null){
                $this->info('filter by user_id');
                $billsSummary = $billsSummary->where('user_id', $options['user_id']);
            }
    
            if($options['from'] != null){
                $this->info('filter from '.$options['period_column']);
                $billsSummary = $billsSummary->whereDate($options['period_column'], '>=', $options['from']);
            }
    
            if($options['to'] != null){
                $this->info('filter to '.$options['period_column']);
                $billsSummary = $billsSummary->whereDate($options['period_column'], '<=', $options['to']);
            }
        }

        $billsSummary = $billsSummary->first();

        $this->info("paid_total = ".$billsSummary->paid_total); 
        $this->info("refund_total = ".$billsSummary->refund_total); 
        $this->info("bills_counter = ".$billsSummary->bills_counter); 

        if($this->confirm('Do you want to export these bills transactions?')){
            $this->info('transactions will exported');
            
            $file_name = 'bills_transaction'.Carbon::now()->timestamp.'.xlsx';
            (new BillsTransactionsExport($options))
            ->store($filePath = 'bills_transactions/'. $file_name, 'local');

            $this->info($file_name);
        }
    }
}
