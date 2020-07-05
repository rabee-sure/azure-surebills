<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('bill_id');
            $table->foreign('bill_id')->on('bills')->references('id');
            $table->string('product_name');
            $table->double('product_price', 10, 2);
            $table->integer('quantity');
            $table->double('total', 10, 2);
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
        Schema::dropIfExists('bill_items');
    }
}
