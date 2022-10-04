<?php

namespace Database\Seeders;

use App\Models\RefundedBill;
use Illuminate\Database\Seeder;

class UpdateRefundedBillsMethods extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $refundedBills = RefundedBill::all();

        foreach($refundedBills as $refundedBill){
            switch ($refundedBill->bill->status) {
                case 'paid':
                    $refundedBill->method = 'online';
                    break;
                case 'refunded':
                    $refundedBill->method = 'online';
                    break;
                case 'paid_cash':
                    $refundedBill->method = 'cash';
                    break;
                case 'refunded_cash':
                    $refundedBill->method = 'cash';
                    break;
                case 'paid_bank_transfer':
                    $refundedBill->method = 'bank_transfer';
                    break;
                case 'refunded_bank_transfer':
                    $refundedBill->method = 'bank_transfer';
                    break;
                
                default:
                    # code...
                    break;
            }

            $refundedBill->save();
            echo $refundedBill->id;
        }
    }
}
