<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPaymentLogsAddColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_logs', function (Blueprint $table) {
            $table->string('brand')->nullable()->after('status')->index();
            $table->string('card_number')->nullable()->after('brand')->index();
            $table->string('bank_transaction_id')->nullable()->after('card_number');
            $table->string('bank_message')->nullable()->after('bank_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
