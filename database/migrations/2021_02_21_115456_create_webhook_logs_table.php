<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebhookLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(0)->index();
            $table->integer('status_code')->default(0)->index();
            $table->json('response');
            $table->json('payload');
            $table->string('error_message')->nullable();

            $table->uuid('bill_id')->nullable()->index();
            $table->foreign('bill_id')
                ->references('id')
                ->on('bills')
                ->onDelete('set null');

            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->unsignedBigInteger('application_id')->nullable()->index();
            $table->foreign('application_id')
                ->references('id')
                ->on('applications')
                ->onDelete('set null');

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
        Schema::dropIfExists('webhook_logs');
    }
}
