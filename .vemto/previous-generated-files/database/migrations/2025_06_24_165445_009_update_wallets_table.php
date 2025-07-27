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
        Schema::table('wallets', function (Blueprint $table) {
            $table->string('walletable_type')->after('currency');

            $table
                ->bigInteger('walletable_id')
                ->unsigned()
                ->after('walletable_type');
            $table->dropColumn('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropColumn('walletable_type');
            $table->dropColumn('walletable_id');

            $table
                ->bigInteger('user_id')
                ->unsigned()
                ->index()
                ->after('id');
        });
    }
};
