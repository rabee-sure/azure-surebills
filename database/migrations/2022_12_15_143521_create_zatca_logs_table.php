<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZatcaLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zatca_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('bill_id')->nullable();
            $table->foreign('bill_id')->references('id')->on('bills')->onDelete('set null');
            $table->json('payload')->nullable();
            $table->string('api')->nullable();
            $table->json('response')->nullable();
            $table->integer('response_code');
            $table->string('reporting_status')->nullable();
            $table->string('clearance_status')->nullable();
            $table->string('disposition_message')->nullable();
            $table->string('status')->nullable();
            $table->string('qrSellert_status')->nullable();
            $table->string('qrBuyert_status')->nullable();
            $table->timestamps();

            $table->index(['api']);
            $table->index(['reporting_status']);
            $table->index(['clearance_status']);
            $table->index(['disposition_message']);
            $table->index(['status']);
            $table->index(['qrSellert_status']);
            $table->index(['qrBuyert_status']);
            
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zatca_logs');
    }
}
