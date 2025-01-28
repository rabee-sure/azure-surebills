<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZatcaInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zatca_invoices', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('number');
            $table->enum('bill_type', ['bill', 'credit_note', 'debit_note']);
            $table->foreignId('merchant_id')->constrained('zatca_merchants')->onDelete('cascade');
            $table->string('merchant_name');
            $table->string('merchant_email');
            $table->string('merchant_vat_registration_number');
            $table->string('merchant_crn');
            $table->string('merchant_tin');
            $table->string('merchant_building_no');
            $table->string('merchant_street_name');
            $table->string('merchant_district');
            $table->string('merchant_city');
            $table->string('merchant_postal_code');
            $table->string('customer_name');
            $table->string('customer_vat_registration_number')->nullable();
            $table->string('customer_building_no')->nullable();
            $table->string('customer_street_name')->nullable();
            $table->string('customer_district')->nullable();
            $table->string('customer_city')->nullable();
            $table->string('customer_postal_code')->nullable();
            $table->double('bill_amount');
            $table->double('tax_value');
            $table->double('vat');
            $table->double('discount')->default(0);
            $table->double('total');
            $table->dateTime('invoice_date');
            $table->text('zatca_qr_code');
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
        Schema::dropIfExists('zatca_invoices');
    }
}
