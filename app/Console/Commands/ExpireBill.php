<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Events\BillStatusUpdated;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExpireBill extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expire:bill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire Bill';

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
        $bills = Bill::pending()
            ->where(function($query) {
                $query->where('expiry_date', '>', 0)
                    ->orWhere('expiry_hours', '>', 0)
                    ->orWhere('expiry_minutes', '>', 0);
            })->get();
            
        $this->info("expire bill comand count: {$bills->count()} working!");
        foreach ($bills as $bill) {
            if($bill->is_expired){   
                $this->info("make Bill id: {$bill->id} expired!");
                $bill->status = 'expired';
                $bill->save();
                event( new BillStatusUpdated($bill) );
            }
        }
    }
}
