<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaymentFeesForChannelsAndSurebillsToBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->double('payment_channel_fees', 10, 2)->nullable();
            $table->double('payment_channel_fees_vat', 10, 2)->nullable();
            $table->double('payment_surebills_fees', 10, 2)->nullable();
            $table->double('payment_surebills_fees_vat', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropColumn([
                'payment_channel_fees',
                'payment_channel_fees_vat',
                'payment_surebills_fees',
                'payment_surebills_fees_vat',
            ]);
        });
    }
}
