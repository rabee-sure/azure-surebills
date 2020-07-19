<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('bill_id')->nullable();
            $table->integer('bill_no');
            $table->datetime('creation_date');
            $table->datetime('payment_date');
            $table->string('description');
            $table->string('reference');
            $table->string('receipt');
            $table->string('auth_id');
            $table->string('card');
            $table->enum('type', ['credit', 'debit']);
            $table->double('balance', 10, 2);
            $table->timestamps();

            $table->foreign('bill_id')
                ->references('id')
                ->on('bills');

            $table->index(['creation_date']);
            $table->index(['payment_date']);
            $table->index(['type']);
            $table->index(['reference']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
