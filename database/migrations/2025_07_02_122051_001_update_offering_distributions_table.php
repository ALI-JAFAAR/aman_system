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
        Schema::table('offering_distributions', function (Blueprint $table) {
            $table->enum('status', ['enabled', 'disabled'])->after('value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offering_distributions', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
