<?php

namespace App\Console\Commands;

use App\Models\Transfer;
use App\Models\User;
use App\Services\TransferService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Valuestore\Valuestore;

class CreateCustomeTransferRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Transfer:customRequest {--user=} {--amount=} {--cycle_date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create transfer request for user with custom amount';

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
        if(!$this->option('user')){
            $this->error('User id required');
        }
        if(!$this->option('amount')){
            $this->error('Transfer amount required');
        }
        if(!$this->option('cycle_date')){
            $this->error('Cycle date required');
        }
        
        $user = User::find($this->option('user'));

        $timestamp = $this->option('cycle_date').' 23:59:59';
        $cycleDate = Carbon::createFromFormat('Y-m-d H:i:s', $timestamp, 'Asia/Riyadh');

        $amount = $this->option('amount');

        $bank = $user->bank;
        $transfer_fees = $bank->fees+ ($bank->fees * 0.15);

        $data = [
            'cycle_date' => $cycleDate,
            'transfer_fees' => $transfer_fees,
            'note' => 'Custom Transfer Via Command',
            'created_by_id' => $user->id,
            'bank_id' => $bank->id,
            'user_id' => $user->id,
            'iban_number' => $user->iban_number,
            'beneficiary_name' => $user->beneficiary_name,
        ];

        $this->line('transfer data will store :');
        $this->line('status = pending');
        $this->line('amount = '.$amount);
        foreach($data as $key => $line){
            $this->line($key.' = '.$line);
        }
        if ($this->confirm('Do you wish to continue?')) {
            $transfer = TransferService::makeTransfer('pending', $amount, $data);
            $this->info('Transfer Request Created');
        }else{
            $this->info('Transfer Request canceled');
        }

    }
}
