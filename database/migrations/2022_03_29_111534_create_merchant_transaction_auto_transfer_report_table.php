<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantTransactionAutoTransferReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_auto_transfer_report', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auto_transfer_id')->references('id')->on('auto_transfers')->onDelete('cascade')->nullable();
            $table->string('created_at')->nullable();
            $table->longText('description')->nullable();
            $table->string('type')->nullable();
            $table->string('amount')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('bill_id')->nullable();
            $table->string('bill_reference_id')->nullable();
            $table->string('bill_number')->nullable();
            $table->string('bill_user_id')->nullable();
            $table->string('bill_business_name')->nullable();
            $table->string('card_brand')->nullable();
            $table->string('card')->nullable();
            $table->string('source')->nullable();
            $table->string('bill_application_channel_id')->nullable();
            $table->string('bill_application_channel_name')->nullable();
            $table->string('report_type')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchant_auto_transfer_report');
    }
}
