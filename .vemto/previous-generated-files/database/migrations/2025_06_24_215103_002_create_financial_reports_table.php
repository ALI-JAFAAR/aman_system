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
        Schema::create('financial_reports', function (Blueprint $table) {
            $table->id();

            $table->string('title');

            $table->enum('report_type', [
                'balance_sheet',
                'profit_loss',
                'cash_flow',
                'custom',
            ]);

            $table->json('parameters');

            $table->string('file_path');

            $table->timestamp('generated_at');

            $table->bigInteger('generated_by')->unsigned();

            $table->text('notes');

            $table->timestamp('created_at')->nullable();

            $table->timestamp('updated_at')->nullable();

            $table->timestamp('deleted_at')->nullable();

            $table
                ->foreign('generated_by')
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
        Schema::dropIfExists('financial_reports');
    }
};
