<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDebitNoteBillIdToBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->uuid('debit_note_bill_id')->nullable();
            $table->foreign('debit_note_bill_id')->on('bills')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropForeign(['debit_note_bill_id']);
            $table->dropColumn('debit_note_bill_id');
        });
    }
}
