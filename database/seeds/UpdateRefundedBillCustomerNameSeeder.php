<?php

namespace Database\Seeders;

use App\Models\RefundedBill;
use Illuminate\Database\Seeder;

class UpdateRefundedBillCustomerNameSeeder extends Seeder
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
            $refundedBill->customer_name = $refundedBill->bill->customer_name;
            $refundedBill->save();
            echo $refundedBill->id.' | ';
        }
    }
}
