<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZatcaInvoiceItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zatca_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->nullable()->constrained('zatca_invoices')->onDelete('cascade');
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
        Schema::dropIfExists('zatca_invoice_items');
    }
}
