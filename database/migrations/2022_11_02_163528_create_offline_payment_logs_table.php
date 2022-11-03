<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfflinePaymentLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offline_payment_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('tx_rslt');
            $table->json('results');
            $table->uuid('bill_id')->nullable();
            $table->foreign('bill_id')->references('id')->on('bills')->onDelete('set null');
            $table->string('payment_method')->nullable();
            $table->index(['payment_method']);
            $table->string('tid');
            $table->string('bank');
            $table->double('amount');
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
        Schema::dropIfExists('offline_payment_logs');
    }
}
