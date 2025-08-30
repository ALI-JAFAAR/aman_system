<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('accounting_periods', function (Blueprint $t) {
            $t->id();
            $t->string('name')->unique();               // مثلا 2025-08
            $t->date('start_date');
            $t->date('end_date');
            $t->boolean('is_closed')->default(false);
            $t->foreignId('closed_by')->nullable()->constrained('employees');
            $t->timestamp('closed_at')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('accounting_periods');
    }
};
