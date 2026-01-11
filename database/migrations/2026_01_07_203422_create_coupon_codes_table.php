<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon_codes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coupon_id');
            $table->foreign('coupon_id')->on('coupons')->references('id')->onDelete('cascade');
            
            $table->string('code')->unique();
            $table->boolean('is_used')->default(false);
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index('coupon_id');
            $table->index('code');
            $table->index('is_used');
            $table->index(['coupon_id', 'is_used']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupon_codes');
    }
}
