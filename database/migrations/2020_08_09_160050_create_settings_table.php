<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            $table->boolean('add_tax');
            $table->string('tax_name')->nullable();  
            $table->integer('tax_value')->nullable();  

            $table->string('default_lang');
            $table->string('active_lang');

            $table->boolean('create_send_sms');
            $table->boolean('create_send_email');            

            $table->boolean('paid_send_sms');
            $table->boolean('paid_send_email');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->on('users')->references('id');

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
        Schema::dropIfExists('settings');
    }
}
