<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FinancialReport;

class FinancialReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FinancialReport::factory()
            ->count(5)
            ->create();
    }
}
