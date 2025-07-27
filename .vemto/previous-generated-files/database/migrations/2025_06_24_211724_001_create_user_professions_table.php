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
        Schema::create('user_professions', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('user_affiliation_id')->unsigned();

            $table->bigInteger('profession_id')->unsigned();

            $table->bigInteger('specialization_id')->unsigned();

            $table->enum('status', ['pending', 'approved', 'rejected']);

            $table->date('applied_at')->nullable();

            $table->date('approved_at')->nullable();

            $table->bigInteger('approved_by')->unsigned();

            $table->text('notes');

            $table->timestamp('created_at')->nullable();

            $table->timestamp('updated_at')->nullable();

            $table->timestamp('deleted_at')->nullable();

            $table
                ->foreign('user_affiliation_id')
                ->references('id')
                ->on('user_affiliations')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table
                ->foreign('profession_id')
                ->references('id')
                ->on('professions')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table
                ->foreign('specialization_id')
                ->references('id')
                ->on('specializations')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table
                ->foreign('approved_by')
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
        Schema::dropIfExists('user_professions');
    }
};
