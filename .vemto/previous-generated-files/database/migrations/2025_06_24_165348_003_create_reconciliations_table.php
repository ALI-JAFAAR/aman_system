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
        Schema::create('reconciliations', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('organization_id')->unsigned();

            $table->bigInteger('contract_id')->unsigned();

            $table->date('period_start');

            $table->date('period_end');

            $table->decimal('total_gross_amount');

            $table->decimal('total_platform_share');

            $table->decimal('total_organization_share');

            $table->decimal('total_partner_share');

            $table->enum('status', ['draft', 'pending_partner', 'confirmed']);

            $table->timestamp('platform_reconciled_at');

            $table->bigInteger('platform_reconciled_by')->unsigned();

            $table->bigInteger('partner_reconciled_by')->unsigned();

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
                ->foreign('contract_id')
                ->references('id')
                ->on('contracts')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table
                ->foreign('platform_reconciled_by')
                ->references('id')
                ->on('employees')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table
                ->foreign('partner_reconciled_by')
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
        Schema::dropIfExists('reconciliations');
    }
};
