<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrganizationSpecialization;

class OrganizationSpecializationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OrganizationSpecialization::factory()
            ->count(5)
            ->create();
    }
}
