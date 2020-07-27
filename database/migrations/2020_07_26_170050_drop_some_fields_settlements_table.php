<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropSomeFieldsSettlementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settlements', function (Blueprint $table) {
            $table->dropColumn('bills_paid_from');
            $table->dropColumn('bills_paid_to');
            $table->dropColumn('total_number_of_bills');
            $table->dropColumn('total_fees_amount');
            $table->dropColumn('total_paid_amount');
            $table->renameColumn('total_amount_of_bills', 'amount');

            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->foreign('created_by_id')->on('users')->references('id');   
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settlements', function (Blueprint $table) { 
            $table->dropForeign(['created_by_id']);
            $table->dropColumn('created_by_id');
            $table->date('bills_paid_from')->nullable();
            $table->date('bills_paid_to')->nullable();
            $table->integer('total_number_of_bills')->nullable();
            $table->double('total_paid_amount', 10, 2)->nullable();
            $table->double('total_fees_amount', 10, 2)->nullable();
            $table->renameColumn('amount', 'total_amount_of_bills');
        });
    }
}
