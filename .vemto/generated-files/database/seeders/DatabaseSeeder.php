<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()
            ->count(1)
            ->create([
                'email' => 'admin@admin.com',
                'password' => \Hash::make('admin'),
            ]);

        $this->call(ClaimSeeder::class);
        $this->call(ClaimResponseSeeder::class);
        $this->call(EmployeeSeeder::class);
        $this->call(OrganizationSeeder::class);
        $this->call(PackageSeeder::class);
        $this->call(PartnerOfferingSeeder::class);
        $this->call(ServiceSeeder::class);
        $this->call(TransactionSeeder::class);
        $this->call(UserAffiliationSeeder::class);
        $this->call(UserOfferingSeeder::class);
        $this->call(UserServiceSeeder::class);
        $this->call(WalletSeeder::class);
        $this->call(LedgerEntrySeeder::class);
        $this->call(ContractSeeder::class);
        $this->call(ReconciliationSeeder::class);
        $this->call(ReconciliationEntrySeeder::class);
        $this->call(AuditLogSeeder::class);
        $this->call(ProjectSeeder::class);
        $this->call(ProjectWorkerSeeder::class);
        $this->call(VehicleSeeder::class);
        $this->call(ProfessionalRecordSeeder::class);
        $this->call(UserProfileSeeder::class);
        $this->call(ProfessionSeeder::class);
        $this->call(SpecializationSeeder::class);
        $this->call(OrganizationSpecializationSeeder::class);
        $this->call(UserProfessionSeeder::class);
        $this->call(HealthAnswerSeeder::class);
        $this->call(AdministrativeRecordSeeder::class);
        $this->call(WithdrawRequestSeeder::class);
        $this->call(NotificationSeeder::class);
        $this->call(BankAccountSeeder::class);
        $this->call(FinancialReportSeeder::class);
    }
}
