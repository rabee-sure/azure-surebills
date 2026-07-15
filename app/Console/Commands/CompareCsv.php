<?php

namespace App\Console\Commands;

use App\Models\PaymentLog;
use App\Imports\HyperPayImport;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class CompareCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'compare:csv';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'compare csv';

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
        $path = $this->ask('What is your file name in "app/public"?');

<<<<<<< HEAD
        if (Storage::disk('public')->has($path)) {
            $data = Excel::toCollection(new HyperPayImport, $path, 'public')[0];
=======
        $excel_file = storage_path('app/public/'.$path);

        if(Storage::has($path)){
            $data = Excel::toCollection(new HyperPayImport, $excel_file)[0];
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4

            $uniqueids = $data->pluck('order_id');
            $payment_log_ids = PaymentLog::whereIn('id', $uniqueids->toArray())
                ->whereStatus(1)
                ->get()
                ->pluck('id');

            $diff = $uniqueids->diff($payment_log_ids);

            $this->info('there is '. $diff->count() . ' recourd dont saved in database');
            $this->info('-------------------------');
            foreach ($diff->all() as $value) {
                $this->line($value);
            }
        }else{
            $this->error('Something went wrong! cant find file');
        }
    }
}
