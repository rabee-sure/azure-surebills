<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusColumnToZatcaInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('zatca_invoices', function (Blueprint $table) {
            $table->enum('status', ['pending','paid','paid_cash','paid_bank_transfer','paid_machine','refunded'])->default('paid')->after('number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('zatca_invoices', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
