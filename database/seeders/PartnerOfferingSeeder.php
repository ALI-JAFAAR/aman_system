<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PartnerOffering;

class PartnerOfferingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PartnerOffering::factory()
            ->count(5)
            ->create();
    }
}
