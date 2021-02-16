<?php

namespace App\Console\Commands;

use App\Imports\HyperPayImport;
use App\Models\PaymentLog;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class FixMedia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:media';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'fix media';

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
        $count =DB::table('media')->where('model_type', 'App\User') ->count();
        $this->info("fixing {$count} media");
        DB::table('media')->where('model_type', 'App\User')
              ->update(['model_type' => 'App\Models\User']);
        $this->info("done");
    }
}
