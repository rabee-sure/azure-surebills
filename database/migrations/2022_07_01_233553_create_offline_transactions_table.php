<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfflineTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offline_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('bill_id')->nullable();
            $table->string('description');
            $table->string('reference');
            $table->unsignedBigInteger('receipt');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->enum('type', ['credit', 'debit']);
            $table->decimal('balance', 15, 8);
            $table->decimal('amount', 15, 8);
            $table->string('transaction_source')->nullable();
            $table->integer('order')->nullable();
            $table->boolean('pending')->default(false);
            $table->timestamps();

            $table->foreign('bill_id')->references('id')->on('bills');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            $table->index(['type']);
            $table->index(['reference']);
            $table->index(['transaction_source']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offline_transactions');
    }
}
