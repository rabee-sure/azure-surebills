<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewRejectedPaymentStatusToBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE bills MODIFY COLUMN status ENUM('pending', 'paid', 'paid_cash', 'paid_bank_transfer', 'canceled', 'expired', 'refunded', 'refunded_cash', 'refunded_bank_transfer', 'failed', 'paid_machine', 'refunded_machine', 'rejected')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bills', function (Blueprint $table) {
            //
        });
    }
}
