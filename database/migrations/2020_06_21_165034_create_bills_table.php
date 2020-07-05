<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('status', ['pending', 'paid', 'canceled', 'expired'])->default('pending');  
            $table->enum('payment_method', ['credit', 'stc', 'apple']);  

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->on('users')->references('id');            

            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->on('customers')->references('id');

            $table->string('business_name');
            $table->string('customer_name');
            $table->string('customer_mobile');
            $table->string('customer_email');

            $table->string('reference_id')->nullable();

            $table->dateTime('due_date', 0)->nullable();  
            $table->integer('expiry_date')->default(0);  

            $table->boolean('add_discount');
            $table->enum('discount_type', ['fixed', 'percentage'])->nullable();  
            $table->integer('discount_value')->nullable();  

            $table->boolean('add_tax');
            $table->string('tax_name')->nullable();  
            $table->integer('tax_value')->nullable();  

            $table->boolean('send_sms');
            $table->boolean('send_email');

            $table->double('sub_total', 10, 2)->default(0);
            $table->double('vat', 10, 2)->default(0);
            $table->double('discount', 10, 2)->default(0);
            $table->double('total', 10, 2)->default(0);

            $table->timestamp('paid_at')->nullable();  
            $table->timestamp('canceled_at')->nullable();  

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
        Schema::dropIfExists('bills');
    }
}
