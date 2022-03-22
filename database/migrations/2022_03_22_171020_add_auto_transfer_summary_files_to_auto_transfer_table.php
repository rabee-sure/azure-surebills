<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAutoTransferSummaryFilesToAutoTransferTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('auto_transfers', function (Blueprint $table) {
            $table->string('due_amount_file')->nullable();
            $table->string('merchants_summary_file')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('auto_transfers', function (Blueprint $table) {
            $table->dropColumn(['due_amount_file', 'merchants_summary_file']);
        });
    }
}
