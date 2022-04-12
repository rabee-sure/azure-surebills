<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePosOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pos_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->on('users')->references('id');
            $table->string('business_name');

            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->on('customers')->references('id');
            $table->string('customer_name');
            $table->string('customer_mobile');
            $table->string('customer_email');
            $table->text('customer_notes')->nullable();
            $table->string('bullding_no')->nullable();
            $table->string('street_name')->nullable();
            $table->string('district')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('additional_no')->nullable();
            $table->string('other_buyer_id')->nullable();
            $table->string('vat_registration_number')->nullable();

            $table->boolean('add_discount');
            $table->enum('discount_type', ['fixed', 'percentage'])->nullable();  
            $table->integer('discount_value')->nullable();

            $table->boolean('add_tax');
            $table->string('tax_name')->nullable();  
            $table->integer('tax_value')->nullable();

            $table->double('sub_total', 10, 2)->default(0);
            $table->double('vat', 10, 2)->default(0);
            $table->double('discount', 10, 2)->default(0);
            $table->double('total', 10, 2)->default(0);

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
        Schema::dropIfExists('pos_orders');
    }
}
