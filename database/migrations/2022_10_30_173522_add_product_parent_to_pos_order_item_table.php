<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductParentToPosOrderItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pos_order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('product_parent')->nullable();
            $table->foreign('product_parent')->on('pos_order_items')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pos_order_items', function (Blueprint $table) {
            $table->dropColumn('product_parent');
        });
    }
}
