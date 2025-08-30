<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void{
        Schema::table('ledger_entries', function (Blueprint $t) {
            $t->unsignedBigInteger('invoice_id')->nullable()->after('id');
            $t->timestamp('posted_at')->nullable()->after('created_by');
            $t->index('invoice_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void{

    }
};
