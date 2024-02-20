<?php

namespace App\Console\Commands;

use App\Models\Bill;
use Illuminate\Console\Command;

class AddFixedTotalToBillsBatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bills:fixed_total_batch {--from=} {--to=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for add fixed amount of bills totals batch';

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
        $from = $this->option('from');
        $to = $this->option('to');

        $paidBills = Bill::whereNotIn('status', ['failed', 'canceled']);

        if($from != null){
            $paidBills = $paidBills->whereDate('created_at', '>=', $from);
        }

        if($to != null){
            $paidBills = $paidBills->whereDate('created_at', '<=', $to);
        }

        $paidBills = $paidBills->orderBy('created_at', 'desc')->pluck('id')->toArray();

        $billsCount = count($paidBills);

        $this->info($billsCount.' bills found need to add fixed total');

        if($this->confirm('Do you wish to fix this bills ?')){
            if($billsCount > 0){
                $chunked_bills = array_chunk($paidBills, 100);
    
                foreach($chunked_bills as $key => $bills){
                    foreach($bills as $bkey => $bill_id){
                        $this->info('Round '.($bkey+1)*($key+1));
                        
                        $bill = Bill::find($bill_id);
                        
                        if(!$bill){
                            $this->error('Bill '.$bill_id.' not found');
                            continue;
                        }
                        
                        $bill->fixed_total = $bill->sub_total - $bill->discount + $bill->vat;
                        $bill->save();

                        $this->line('Bill '.$bill_id.' fixed total: '.$bill->fixed_total.' added succefully');
                    }
                }

                $this->info('All Bills fixed total added Successfuly');
            }
        }
    }
}
