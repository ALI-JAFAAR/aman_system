<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
public function up(): void
{
Schema::table('users', function (Blueprint $table) {
$table->foreignId('parent_id')
->nullable()
->constrained('users')
->cascadeOnDelete();

$table->string('family_relation')->nullable(); // 'spouse','son','daughter','parent',...
});
}

public function down(): void
{
Schema::table('users', function (Blueprint $table) {
$table->dropConstrainedForeignId('parent_id');
$table->dropColumn('family_relation');
});
}
};
