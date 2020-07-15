<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettlementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settlements', function (Blueprint $table) {
            $table->id();
            $table->date('bills_paid_from');
            $table->date('bills_paid_to');
            $table->integer('total_number_of_bills');
            $table->double('total_amount_of_bills', 10, 2);
            $table->double('total_paid_amount', 10, 2);
            $table->double('total_fees_amount', 10, 2);
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->on('users')->references('id');     
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
        Schema::dropIfExists('settlements');
    }
}
