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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('organization_id')->unsigned();

            $table->enum('service_type', [
                'identity_issue',
                'route_card',
                'claim',
                'other',
                'certifcate',
            ]);

            $table->enum('initiator_type', ['platform', 'partner']);

            $table->decimal('platform_rate')->nullable();

            $table->decimal('organization_rate')->nullable();

            $table->decimal('partner_rate');

            $table->date('contract_start');

            $table->date('contract_end')->nullable();

            $table->text('notes');

            $table->timestamp('created_at')->nullable();

            $table->timestamp('updated_at')->nullable();

            $table->timestamp('deleted_at')->nullable();

            $table
                ->foreign('organization_id')
                ->references('id')
                ->on('organizations')
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
        Schema::dropIfExists('contracts');
    }
};
