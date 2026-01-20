<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponUsagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coupon_id');
            $table->foreign('coupon_id')->on('coupons')->references('id')->onDelete('cascade');
            
            $table->unsignedBigInteger('coupon_code_id')->nullable()->comment('For ONE_TIME_USAGE mechanism');
            $table->foreign('coupon_code_id')->on('coupon_codes')->references('id')->onDelete('cascade');
            
            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->on('customers')->references('id')->onDelete('cascade');
            
            $table->uuid('bill_id')->nullable()->comment('Reference to the bill where coupon was used');
            $table->foreign('bill_id')
                ->references('id')
                ->on('bills')
                ->onDelete('set null');
            
            $table->dateTime('used_at');
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index('coupon_id');
            $table->index('coupon_code_id');
            $table->index('customer_id');
            $table->index('bill_id');
            $table->index('used_at');
            $table->index(['coupon_id', 'customer_id']);
            $table->index(['coupon_code_id', 'customer_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupon_usages');
    }
}
