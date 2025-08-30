<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void{
        Schema::create('offering_distributions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('partner_offering_id');
            $table->unsignedBigInteger('organization_id')->nullable();

            // نسب التوزيع
            $table->decimal('partner_percent',  5, 2)->default(0);
            $table->decimal('platform_percent', 5, 2)->default(100);
            $table->decimal('host_org_percent', 5, 2)->default(0);

            $table->enum('status', ['active','inactive'])->default('active');

            $table->timestamps();
            $table->softDeletes();

            // مفاتيح أجنبية بأسماء قصيرة
            $table->foreign('partner_offering_id', 'od_po_fk')
                ->references('id')->on('partner_offerings')
                ->cascadeOnUpdate()->cascadeOnDelete();

            $table->foreign('organization_id', 'od_org_fk')
                ->references('id')->on('organizations')
                ->nullOnDelete();

            // فهرس UNIQUE باسم قصير
            $table->unique(['partner_offering_id','organization_id'], 'od_po_org_uq');
        });
    }
    public function down(): void{
        Schema::dropIfExists('offering_distributions');
    }
};
