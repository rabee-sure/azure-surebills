<?php

namespace App\Console\Commands;

use App\Models\Transfer;
use Illuminate\Console\Command;

class ManualTransferBulk extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:manual_bulk {start_date} {end_date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send bulk transfers to sps manual for specifec period';

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
        $start_date = $this->argument('start_date');
        $end_date = $this->argument('end_date');

        $target_transfers = Transfer::whereDate('created_at', '>=', $start_date)
        ->whereDate('created_at', '<=', $end_date)
        ->where('status', 'send_to_sps')
        ->get();
        
        if($target_transfers->count() > 0){
            $this->info("Found {$target_transfers->count()} sps transfers");
            $i = 0;
            foreach($target_transfers as $transfer){
                $i++;
                $body = [];
                $transfer_payload = [
                    'referenceNumber' => (string) $transfer->id,
                    'amount' => $transfer->net_amount,
                    'beneficiaryName' => $transfer->user->beneficiary_name,
                    'beneficiaryIban' => $transfer->user->iban_number,
                    'beneficiaryStreet' => $transfer->user->business_address,
                    'beneficiaryCountry' => 'SA',
                    'beneficiaryBank' => $transfer->user->bank->code,
                    "isSynced" => true,
                    "transferRequest" => "string",
                    "transferResponse" => "string",
                    "transferStatusId" => 0,
                    "transferStatusName" => "string",
                    "beneficiaryCity" => "riyadh"
                ];
        
                $body[] = $transfer_payload;
        
                $url = 'https://surebill-api.surepay.sa/api/Transfer/Transfer';
        
                \Log::channel('send_to_sps')->info("request body", $body);
        
                $transfers = [
                    'transfers' => $body
                ];
        
                $postData = json_encode($transfers);
        
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,$url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                curl_setopt($ch, CURLOPT_POSTFIELDS,$postData);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                $server_output = curl_exec($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close ($ch);
        
                \Log::channel('send_to_sps')->info("SPS response - Manual Command", ['transfer_id' => $transfer->id]);
                \Log::channel('send_to_sps')->info("SPS response - Manual Command", ['code' => $httpcode]);
                \Log::channel('send_to_sps')->info("SPS response - Manual Command", ['response' => $server_output]);
        
                $this->info("SPS Response id: {$transfer->id}");
                $this->info("SPS Response code: {$httpcode}");
                $this->info("SPS Response: {$server_output}");
                $this->info("--------------------------------");
            }
        }else{
            $this->info("No sps transfers in this period");
        }

    }
}
