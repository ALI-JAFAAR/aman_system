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
        Schema::create('claims', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('user_offering_id')->unsigned();

            $table->enum('type', ['health', 'legal', 'financial']);

            $table->text('details');

            $table->date('accident_date')->nullable();

            $table->bigInteger('amount_requested');

            $table->enum('status', [
                'pending',
                'needs_info',
                'approved',
                'rejected',
            ]);

            $table->bigInteger('resolution_amount');

            $table->text('resolution_note')->nullable();

            $table->timestamp('submitted_at');

            $table->timestamp('created_at')->nullable();

            $table->timestamp('updated_at')->nullable();

            $table->timestamp('deleted_at')->nullable();

            $table
                ->foreign('user_offering_id')
                ->references('id')
                ->on('user_offerings')
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
        Schema::dropIfExists('claims');
    }
};
