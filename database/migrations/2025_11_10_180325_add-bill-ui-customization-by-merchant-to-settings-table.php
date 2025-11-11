<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBillUiCustomizationByMerchantToSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('background_color_body')->nullable();
            $table->string('background_image_file')->nullable();
            $table->string('text_color_body')->nullable();
            $table->string('background_color_payment_button')->nullable();
            $table->string('text_color_payment_button')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['background_color_body', 'background_image_file', 'text_color_body', 'background_color_payment_button', 'text_color_payment_button']);
        });
    }
}
