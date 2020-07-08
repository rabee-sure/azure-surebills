<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('secret')->nullable();
            $table->text('redirect')->nullable();
            $table->text('fail_redirect_url')->nullable();
            $table->text('webhook_url')->nullable();
            $table->string('webhook_secret')->nullable();

            $table->unsignedBigInteger('user_id');
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
        Schema::dropIfExists('applications');
    }
}
