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
        Schema::create('reconciliation_entries', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('reconciliation_id')->unsigned();

            $table->bigInteger('ledger_entry_id')->unsigned();

            $table->timestamp('created_at')->nullable();

            $table->timestamp('updated_at')->nullable();

            $table->timestamp('deleted_at')->nullable();

            $table
                ->foreign('reconciliation_id')
                ->references('id')
                ->on('reconciliations')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table
                ->foreign('ledger_entry_id')
                ->references('id')
                ->on('ledger_entries')
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
        Schema::dropIfExists('reconciliation_entries');
    }
};
