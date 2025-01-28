<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSelfForginToZatcaInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('zatca_invoices', function (Blueprint $table) {
            $table->foreignId('refrence_bill_id')->nullable()->constrained('zatca_invoices')->onDelete('cascade');
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
            $table->dropForeign(['refrence_bill_id']);
            $table->dropColumn('refrence_bill_id');
        });
    }
}
