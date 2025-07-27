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
        Schema::create('ledger_entries', function (Blueprint $table) {
            $table->id();

            $table->string('reference_type');

            $table->bigInteger('reference_id')->unsigned();

            $table->string('account_code');

            $table->enum('entry_type', ['debit', 'credit']);

            $table->decimal('amount');

            $table->text('description')->nullable();

            $table->bigInteger('created_by')->unsigned();

            $table->boolean('is_locked')->default(false);

            $table->timestamp('created_at')->nullable();

            $table->timestamp('updated_at')->nullable();

            $table->timestamp('deleted_at')->nullable();

            $table
                ->foreign('created_by')
                ->references('id')
                ->on('employees')
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
        Schema::dropIfExists('ledger_entries');
    }
};
