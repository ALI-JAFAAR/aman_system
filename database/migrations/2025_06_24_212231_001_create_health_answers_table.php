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
        Schema::create('health_answers', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('user_service_id')->unsigned();

            $table->string('question_key');

            $table->enum('answer', ['نعم', 'كلا']);

            $table->timestamp('created_at')->nullable();

            $table->timestamp('updated_at')->nullable();

            $table->timestamp('deleted_at')->nullable();

            $table
                ->foreign('user_service_id')
                ->references('id')
                ->on('user_services')
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
        Schema::dropIfExists('health_answers');
    }
};
