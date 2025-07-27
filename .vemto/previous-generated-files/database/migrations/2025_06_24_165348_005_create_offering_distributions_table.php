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
        Schema::create('offering_distributions', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('organization_id')->unsigned();

            $table->bigInteger('partner_offering_id')->unsigned();

            $table
                ->enum('mode', ['percentage', 'fixed'])
                ->default('percentage');

            $table->decimal('value');

            $table->timestamp('created_at')->nullable();

            $table->timestamp('updated_at')->nullable();

            $table->timestamp('deleted_at')->nullable();

            $table
                ->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table
                ->foreign('partner_offering_id')
                ->references('id')
                ->on('partner_offerings')
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
        Schema::dropIfExists('offering_distributions');
    }
};
