<?php

namespace App\Jobs;

use App\Models\Bill;
use App\Models\PaymentLog;
use GuzzleHttp\Client;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * SPS transaction forwarder.
 *
 * PR-08: Restored parseability under PHP 8.3. Incomplete `$transaction->;`
 * expressions from the legacy stub were invalid PHP and blocked autoload.
 * Dispatch sites remain commented; this class is not executed in production
 * paths today. Do not redesign SPS here.
 */
class SendTransactionToSPS
{
    use Dispatchable, SerializesModels;

    protected $bill;

    protected $log;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Bill $bill, PaymentLog $payment_log)
    {
        $this->bill = $bill;
        $this->log = $payment_log;
    }

    /**
     * Execute the job.
     *
     * Incomplete legacy stub — fields that referenced undefined $transaction
     * remain commented until SPS wiring is intentionally restored.
     *
     * @return mixed
     */
    public function handle()
    {
        // $logResualt = json_decode($this->log->results);

        // Prepare API data (partial legacy stub)
        // $data['TrxNumber'] = $transaction->id;
        // $data['TrxType'] = $transaction->type;
        // $data['TrxDate'] = $transaction->created_at;
        $data['MaskedCard'] = $this->log->card_number;
        // $data['Amount'] = $logResualt['amount'];
        // $data['NetAmount'] = $transaction->amount;
        // $data['Vat'] = $transaction->;
        // $data['VatPercentage'] = $transaction->;
        // $data['AuthCode'] = $transaction->;
        $data['CardType'] = $this->log->brand;
        // $data['ReconciliationDate'] = $transaction->;
        // $data['ReconciliationNo'] = $transaction->;
        // $data['TrxCertificate'] = $transaction->;
        // $data['Fees'] = $transaction->;
        // $data['MerchantName'] = $transaction->;
        // $data['MerchantId'] = $transaction->;

        // Send transaction data to sps api
        $link = config('sps.base_url').'/'.config('sps.routes.Save_transaction');
        $client = new Client;
        $response = $client->request('POST', $link, ['body' => json_encode($data)]);

        // Log Api failed response

        return $response;
    }
}
