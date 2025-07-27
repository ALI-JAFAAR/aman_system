<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('wallet_id')->unsigned();

            $table->enum('transaction_type', [
                'credit',
                'debit',
                'transfer',
                'withdraw',
                'deposit',
            ]);

            $table->decimal('amount');

            $table
                ->bigInteger('target_wallet_id')
                ->unsigned()
                ->nullable();

            $table
                ->enum('status', ['completed', 'pending', 'failed'])
                ->default('completed');

            $table->string('reference_type')->nullable();

            $table->bigInteger('reference_id')->unsigned();

            $table->text('description')->nullable();

            $table->timestamp('created_at')->nullable();

            $table->timestamp('updated_at')->nullable();

            $table->timestamp('deleted_at')->nullable();

            $table
                ->foreign('wallet_id')
                ->references('id')
                ->on('wallets')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table
                ->foreign('target_wallet_id')
                ->references('id')
                ->on('wallets')
                ->onDelete('cascade')
                ->onUpdate('cascade');
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
};
