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
        Schema::create('claim_responses', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('claim_id')->unsigned();

            $table->enum('action', [
                'request_info',
                'provide_info',
                'approve',
                'reject',
                'legal_contract',
                'user_accept_contract',
            ]);

            $table->enum('actor_type', ['employee', 'user']);

            $table->bigInteger('actor_id')->unsigned();

            $table->text('message');

            $table->timestamp('created_at')->nullable();

            $table->timestamp('updated_at')->nullable();

            $table->timestamp('deleted_at')->nullable();

            $table
                ->foreign('claim_id')
                ->references('id')
                ->on('claims')
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
        Schema::dropIfExists('claim_responses');
    }
};
