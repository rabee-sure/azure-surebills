<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAutoTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auto_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('day');
            $table->text('folder')->nullable();
            $table->text('zip_file')->nullable();
            $table->text('merchants_file')->nullable();
            $table->text('channels_file')->nullable();
            $table->json('tranfer_ids')->nullable();
            $table->timestamps();
        });

        Schema::create('auto_transfer_transfer', function (Blueprint $table) {
            $table->unsignedBigInteger('auto_transfer_id');
            $table->foreign('auto_transfer_id')->on('auto_transfers')->references('id');   
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
        Schema::dropIfExists('auto_transfer_transfer');
        Schema::dropIfExists('auto_transfers');
    }
}
