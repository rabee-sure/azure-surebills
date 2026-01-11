<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->on('users')->references('id')->onDelete('cascade');
            
            $table->string('name');
            $table->enum('mechanism', ['max_usage', 'max_customer_usage', 'one_time_usage']);
            $table->enum('discount_type', ['fixed', 'percentage']);
            $table->decimal('discount_value', 10, 2);
            
            $table->dateTime('valid_from')->nullable();
            $table->dateTime('valid_to')->nullable();
            
            $table->unsignedInteger('max_usage')->nullable()->comment('For MAX_USAGE mechanism');
            $table->unsignedInteger('max_customer_usage')->nullable()->comment('For MAX_CUSTOMER_USAGE mechanism');
            $table->string('code_pattern')->nullable()->comment('Reusable code for MAX_USAGE or pattern for ONE_TIME_USAGE');
            
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index('user_id');
            $table->index('mechanism');
            $table->index('is_active');
            $table->index(['valid_from', 'valid_to']);
            $table->index('code_pattern');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupons');
    }
}
