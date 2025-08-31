<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use App\Models\User;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\Package;
use App\Models\PartnerOffering;
use App\Models\Contract;
use App\Models\Profession;
use App\Models\Specialization;
use App\Models\Service;
use Spatie\Permission\Models\Role;

class DemoCoreSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        // 1) حساب مدير + دور Super Admin (متوافق مع Shield)
        $admin = User::firstOrCreate(
            ['email' => 'admin@demo.test'],
            ['name' => 'Demo Admin', 'password' => Hash::make('password')]
        );

        // Filament Shield غالبًا يستخدم "Super Admin" أو "super_admin"
        $super1 = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $super2 = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $admin->syncRoles([$super1->name, $super2->name]);

        // 2) منظمات أساسية
        $host = Organization::firstOrCreate(
            ['code' => 'HOST1'],
            ['name' => 'الجهة المضيفة', 'type' => 'organization']
        );

        $federation = Organization::firstOrCreate(
            ['code' => 'FED1'],
            ['name' => 'الاتحاد العام', 'type' => 'general_union']
        );

        $guild = Organization::firstOrCreate(
            ['code' => 'GUILD1'],
            ['name' => 'نقابة المهندسين', 'type' => 'guild', 'organization_id' => $federation->id]
        );

        $insurer = Organization::firstOrCreate(
            ['code' => 'INS1'],
            ['name' => 'شركة الأمان للتأمين', 'type' => 'insurance_company']
        );

        // 3) عقد فعّال لأي تسويات مستقبلية (مطلوب auto-pick)
        $start = Carbon::now()->subMonth()->startOfDay();
        $end   = Carbon::now()->addYear()->endOfDay();

        Contract::updateOrCreate(
        // معايير التعرّف على العقد الفعّال (لا نستخدم code ولا status)
            [
                'organization_id' => $host->id,   // غيّر $host لما يناسبك (جهة الاستضافة)
                'contract_start'  => $start,
                'contract_end'    => $end,
            ],
            // القيم المطلوب حفظها
            [
                'service_type'        => 'other',   // غيّرها إن كان عندك values أخرى
                'initiator_type'      => 'platform',       // host | partner … حسب موديلك
                'platform_rate'       => 10,           // يجب أن تملأ القيم الإلزامية
                'organization_rate'   => 90,
                'partner_rate'        => 0,

                // إن كانت هذه الحقول NOT NULL عندك، أعطها 0
                'platform_share'      => 0,
                'organization_share'  => 0,
                'partner_share'       => 0,

                // لو العمود nullable اتركه null
                'partner_offering_id' => null,

                'notes'               => 'Seeded active contract',
            ]
        );

        // 4) مهنة/اختصاص
        $prof = Profession::firstOrCreate(['name' => 'مهندس مدني']);
        $spec = Specialization::firstOrCreate(['name' => 'إنشائي', 'profession_id' => $prof->id]);

        // 5) باقة + عرض شريك فعّال اليوم
        $pkg = Package::firstOrCreate(
            ['name' => 'Basic Health'],
            ['description' => 'تغطية أساسية للرعاية الصحية.']
        );

        $po = PartnerOffering::firstOrCreate(
            [
                'organization_id'          => $insurer->id,
                'package_id'               => $pkg->id,
            ],
            [
                'price'                    => 25000,
                'partner_must_fill_number' => 1,
                'contract_start'           => now()->subMonth()->toDateString(),
                'contract_end'             => now()->addYear()->toDateString(),
            ]
        );

        // 6) خدمة اختيارية
        Service::firstOrCreate(
            ['name' => 'استخراج هوية منتسب'],
            [
                'base_price'     => 10000,
                'is_active'      => true,
                'description'    => 'إصدار هوية جديدة للمنتسب.',
                'request_schema' => [
                    ['key' => 'notes', 'label' => 'ملاحظات', 'type' => 'textarea', 'required' => false],
                ],
            ]
        );

        // 7) اجعل المدير موظفًا لدى الجهة المضيفة (لتمييز سجلاته)
        Employee::firstOrCreate(
            ['user_id' => $admin->id],
            ['organization_id' => $host->id, 'job_title' => 'Administrator','salary'=>1000]
        );

        DB::commit();
    }
}
