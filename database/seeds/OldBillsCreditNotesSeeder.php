<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\RefundedBill;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class OldBillsCreditNotesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $BillsHasRefundTransactions = Transaction::where('transaction_source', 'refund')->where('description', 'like', 'REFUND Bill%')->whereDate('created_at', '<=', date('Y-m-d', strtotime('2022-10-05')))->get();

        $BillsHasPartialRefundTransactions = Transaction::where('transaction_source', 'refund')->where('description', 'like', 'PARTIAL REFUND Bill%')->whereDate('created_at', '<=', date('Y-m-d', strtotime('2022-10-05')))->get();

        $i = 0;
        foreach($BillsHasRefundTransactions as $transaction){
            $creditNote = RefundedBill::where('bill_id', $transaction->bill_id)->count();
            if($creditNote == 0){
                $i++;
                $method = $transaction->bill->getRefundedMethod();
                $refundedBill = RefundedBill::create([
                    'bill_id' => $transaction->bill_id,
                    'user_id' => $transaction->user_id,
                    'amount' => $transaction->amount,
                    'status' => 'cn_refunded',
                    'method' => $method,
                    'customer_name' => $transaction->bill->customer_name
                ]);

                $refundedBill->number = $refundedBill->getNumber();
                $refundedBill->created_at = $transaction->created_at;
                $refundedBill->updated_at = $transaction->created_at;
                $refundedBill->save();

                echo $i."- Bill id : ".$transaction->bill_id." | Total Refunded Amount : ".$transaction->amount." | Created_at : ".$transaction->created_at."\n";
            }
        }

        $i = 0;
        foreach($BillsHasPartialRefundTransactions as $transaction){
            $creditNote = RefundedBill::where('bill_id', $transaction->bill_id)->count();
            if($creditNote == 0){
                $i++;
                $method = $transaction->bill->getRefundedMethod();
                $refundedBill = RefundedBill::create([
                    'bill_id' => $transaction->bill_id,
                    'user_id' => $transaction->user_id,
                    'amount' => $transaction->amount,
                    'status' => 'cn_refunded',
                    'method' => $method,
                    'customer_name' => $transaction->bill->customer_name
                ]);

                $refundedBill->number = $refundedBill->getNumber();
                $refundedBill->created_at = $transaction->created_at;
                $refundedBill->updated_at = $transaction->created_at;
                $refundedBill->save();

                echo $i."- Bill id : ".$transaction->bill_id." | Partial Refunded Amount : ".$transaction->amount." | Created_at : ".$transaction->created_at."\n";
            }
        }
    }
}
