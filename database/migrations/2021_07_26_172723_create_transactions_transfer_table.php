<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTransferTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_transfer', function (Blueprint $table) {
            $table->uuid('transaction_id');
            $table->foreign('transaction_id')->on('transactions')->references('id');   
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
        Schema::dropIfExists('transaction_transfer');
    }
}
