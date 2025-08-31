<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ResetAndSeedDemo extends Command
{
    protected $signature = 'app:reset-demo
        {--drop-user-1 : احذف المستخدم ذي المعرّف 1 بعد الزرع}
        {--hard : امسح أيضًا القواميس (غير مستحسن)}';

    protected $description = 'يمسح بيانات المعاملات ويزرع بيانات تجريبية جاهزة مع حساب مدير Super Admin';

    public function handle(): int
    {
        if (app()->environment('production')) {
            $this->error('ممنوع التشغيل على بيئة الإنتاج.');
            return self::FAILURE;
        }

        $this->warn('سيتم مسح بيانات المعاملات. تأكد من أخذ نسخة احتياطية إن لزم.');
        if (! $this->confirm('هل أنت متأكد؟')) {
            return self::INVALID;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // جداول المعاملات فقط (يمكن تعديل القائمة حسب مشروعك)
        $transactional = [
            'ledger_entries',
            'reconciliation_entries',
            'reconciliations',
            'invoice_items',
            'invoices',
            'user_offerings',
            'user_services',
            'user_affiliations',
            'wallets',
            'payments',           // إن وُجد
            'related_workers',    // إن وُجد
            'password_reset_tokens',
            'job_batches', 'jobs', 'failed_jobs',
            // لو عندك إشعارات بريدية/تنبيهات:
            'notifications',
        ];

        // عند --hard سنمسح كذلك القواميس (عادة لا تحتاجه)
        $dictionaries = [
            // احذفها فقط عند الحاجة الفعلية
            // 'organizations', 'packages', 'partner_offerings', 'contracts',
            // 'professions', 'specializations', 'services',
        ];

        foreach ($transactional as $table) {
            if ($this->tableExists($table)) {
                DB::table($table)->truncate();
                $this->line("Truncated: {$table}");
            }
        }

        if ($this->option('hard')) {
            foreach ($dictionaries as $table) {
                if ($this->tableExists($table)) {
                    DB::table($table)->truncate();
                    $this->line("Truncated (hard): {$table}");
                }
            }
        }

        // (اختياري) إعادة تعيين المستخدمين/الموظفين
        // لا نمسح المستخدمين حتى لا تفقد دخولك. يمكن حذف user=1 بالاختيار.
        if ($this->option('drop-user-1') && $this->tableExists('users')) {
            DB::table('users')->where('id', 1)->delete();
            $this->line('Deleted user #1');
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // شغّل Seeder
        $this->call('db:seed', ['--class' => 'Database\\Seeders\\DemoCoreSeeder', '--force' => true]);

        $this->info('انتهى الإعداد. بيانات تجريبية جهّزت.');
        $this->line('الدخول:  admin@demo.test  |  كلمة المرور: password');
        $this->line('إن كنت تستخدم Filament Shield: سيظهر لك دور Super Admin جاهز.');
        return self::SUCCESS;
    }

    private function tableExists(string $name): bool
    {
        $database = DB::getDatabaseName();
        return DB::selectOne(
                'SELECT COUNT(*) c FROM information_schema.tables WHERE table_schema = ? AND table_name = ?',
                [$database, $name]
            )->c > 0;
    }
}
