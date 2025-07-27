<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdministrativeRecord;

class AdministrativeRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AdministrativeRecord::factory()
            ->count(5)
            ->create();
    }
}
