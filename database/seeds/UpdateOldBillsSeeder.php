<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateOldBillsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('bills')->whereIn('status', ['paid', 'refunded'])->update(['payment_way' => 'online']);
        DB::table('bills')->whereIn('status', ['paid_cash', 'refunded_cash'])->update(['payment_way' => 'cash']);
        DB::table('bills')->whereIn('status', ['paid_bank_transfer', 'refunded_bank_transfer'])->update(['payment_way' => 'bank_transfer']);
        DB::table('bills')->whereNotNull('application_id')->update(['source' => 'api']);
        DB::table('bills')->whereNull('application_id')->update(['source' => 'sure_bill']);
    }
}
