<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDueAmountAutoTransferReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('due_amount_auto_transfer_report', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auto_transfer_id')->references('id')->on('auto_transfers')->onDelete('cascade')->nullable();
            $table->string('merchant_id')->nullable();
            $table->string('merchant_name')->nullable();
            $table->string('merchant_iban')->nullable();
            $table->string('bank')->nullable();
            $table->string('total_amount')->nullable();
            $table->string('total_fees')->nullable();
            $table->string('total_fees_vat')->nullable();
            $table->string('total_refund')->nullable();
            $table->string('bank_charges')->nullable();
            $table->string('net_due')->nullable();
            $table->string('channel_id')->nullable();
            $table->string('reference')->nullable();
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
        Schema::dropIfExists('due_amount_auto_transfer_report');
    }
}
