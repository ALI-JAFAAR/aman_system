<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->foreignId('partner_offering_id')
                ->nullable()
                ->constrained('partner_offerings')
                ->cascadeOnDelete();

            // حصص كل طرف
            $table->decimal('platform_share',     12, 2)->default(0);
            $table->decimal('organization_share', 12, 2)->default(0);
            $table->decimal('partner_share',      12, 2)->default(0);

            // رقم إصدار العقد (versioning)
            $table->unsignedInteger('contract_version')->default(1);

            // ضمان فريد للتكرار غير المقصود
            $table->unique(
                ['organization_id','partner_offering_id','initiator_type','contract_version'],
                'contracts_org_partner_initiator_version_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropUnique('contracts_org_partner_initiator_version_unique');
            $table->dropColumn([
                'partner_offering_id',
                'initiator_type',
                'platform_share',
                'organization_share',
                'partner_share',
                'contract_version',
            ]);
        });
    }
};
