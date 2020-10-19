<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillTransferTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_transfer', function (Blueprint $table) {
            $table->uuid('bill_id');
            $table->foreign('bill_id')->on('bills')->references('id');   
            $table->unsignedBigInteger('transfer_id');
            $table->foreign('transfer_id')->on('settlements')->references('id');       
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bill_transfer');
    }
}
