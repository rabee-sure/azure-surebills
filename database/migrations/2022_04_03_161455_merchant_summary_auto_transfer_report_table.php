<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MerchantSummaryAutoTransferReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_summary_auto_transfer_report', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auto_transfer_id')->references('id')->on('auto_transfers')->onDelete('cascade')->nullable();
            $table->string('client_id')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('no_of_trx')->nullable();
            $table->string('total_amount')->nullable();
            $table->string('total_fees')->nullable();
            $table->string('total_fees_vat')->nullable();
            $table->string('total_fees_variable_rate')->nullable();
            $table->string('total_fees_fixed_rate')->nullable();
            $table->string('sure_variable_rate')->nullable();
            $table->string('sure_fixed_rate')->nullable();
            $table->string('channel_variable_rate')->nullable();
            $table->string('channel_fixed_rate')->nullable();
            $table->string('sure_fees')->nullable();
            $table->string('sure_vat')->nullable();
            $table->string('channel_fees')->nullable();
            $table->string('channels_vat')->nullable();
            $table->string('channel_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchant_summary_auto_transfer_report');
    }
}
