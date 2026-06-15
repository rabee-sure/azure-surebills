<?php

namespace App\Console\Commands;

use App\Models\PaymentLog;
use App\Imports\HyperPayImport;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class CompareExcelSheet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'compare:excel_sheet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'compare excel sheets';

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

        if (Storage::disk('public')->has($path)) {
            $data = Excel::toCollection(new HyperPayImport, $path, 'public')[0];

            $uniqueids = $data->pluck('uniqueid')
                ->map(fn($item, $key) => str_replace(["'", '"'], '', $item));
            $payment_log_ids = PaymentLog::whereIn('results->response->id', $uniqueids->toArray())
                ->get()
                ->pluck('results.response.id');

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
