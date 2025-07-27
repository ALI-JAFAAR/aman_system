<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProfessionalRecord;

class ProfessionalRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProfessionalRecord::factory()
            ->count(5)
            ->create();
    }
}
