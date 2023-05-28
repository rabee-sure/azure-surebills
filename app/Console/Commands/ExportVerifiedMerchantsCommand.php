<?php

namespace App\Console\Commands;

use App\Jobs\ExportVerifiedMerchantsJob;
use Illuminate\Console\Command;

class ExportVerifiedMerchantsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:verified_merchants';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export verified merchants';

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
        dispatch(new ExportVerifiedMerchantsJob());
        dd('success');
        return 0;
    }
}
