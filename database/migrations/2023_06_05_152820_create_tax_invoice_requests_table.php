<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaxInvoiceRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tax_invoice_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->on('users')->references('id');
            $table->enum('status', ['pending', 'on_proccessing', 'on_hold', 'done']);
            $table->timestamps();
            
            $table->timestamp('start_procces_at')->nullable();
            $table->unsignedBigInteger('start_by')->nullable();
            $table->foreign('start_by')->on('admins')->references('id');
            
            $table->timestamp('hold_at')->nullable();
            $table->unsignedBigInteger('hold_by')->nullable();
            $table->foreign('hold_by')->on('admins')->references('id');
            
            $table->timestamp('done_at')->nullable();
            $table->unsignedBigInteger('done_by')->nullable();
            $table->foreign('done_by')->on('admins')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tax_invoice_requests');
    }
}
