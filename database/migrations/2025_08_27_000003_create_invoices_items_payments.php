<?php

// database/migrations/2025_08_27_000003_create_invoices_items_payments.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            // العميل (المنتسب)
            $table->unsignedBigInteger('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

            // الموظف المُصدِّر
            $table->unsignedBigInteger('issuer_employee_id')->nullable()->index();
            $table->foreign('issuer_employee_id')->references('id')->on('employees')->nullOnDelete();

            // رقم الفاتورة وعناصر العرض
            $table->string('number')->unique();               // يمكنك توليده بسلسلة
            $table->timestamp('issued_at')->nullable();

            // ملخصات
            $table->decimal('subtotal', 12, 2)->default(0);   // قبل الخصم/الضرائب (إن وُجدت)
            $table->enum('discount_type', ['none', 'percent', 'fixed'])->default('none');
            $table->decimal('discount_value', 12, 2)->default(0);
            $table->enum('discount_funded_by', ['platform', 'partner', 'host', 'shared'])->default('platform');

            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);      // بعد الخصم
            $table->decimal('paid', 12, 2)->default(0);       // المقبوض
            $table->decimal('balance', 12, 2)->default(0);    // المتبقي

            $table->enum('status', ['unpaid', 'partial', 'paid', 'void'])->default('unpaid');
            $table->string('currency', 3)->default('IQD');

            $table->json('meta')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->foreign('invoice_id')->references('id')->on('invoices')->cascadeOnDelete();

            // نوع البند ومصدره
            $table->enum('item_type', ['affiliation_fee', 'offering', 'service']);
            $table->nullableMorphs('reference'); // reference_type + reference_id (UserAffiliation/UserOffering/…)
            $table->unsignedBigInteger('organization_id')->nullable(); // الجهة المستضيفة إن لزم

            // الوصف والكميات
            $table->string('description')->nullable();
            $table->decimal('qty', 8, 2)->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('line_total', 12, 2)->default(0);

            // لقطة للتوزيع (للتقارير)
            $table->decimal('partner_share', 12, 2)->default(0);
            $table->decimal('host_share', 12, 2)->default(0);
            $table->decimal('platform_share', 12, 2)->default(0);

            $table->json('distribution_snapshot')->nullable();

            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('invoice_id')->index();
            $table->foreign('invoice_id')->references('id')->on('invoices')->cascadeOnDelete();

            $table->unsignedBigInteger('user_id')->index();   // الدافع (عادة نفس العميل)
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

            $table->enum('method', ['cash', 'pos', 'zaincash', 'bank'])->default('cash');
            $table->decimal('amount', 12, 2);
            $table->string('reference')->nullable();          // رقم قسيمة POS أو مرجع زين كاش
            $table->json('meta')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
