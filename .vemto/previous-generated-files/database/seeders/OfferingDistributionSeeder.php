<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OfferingDistribution;

class OfferingDistributionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        OfferingDistribution::factory()
            ->count(5)
            ->create();
    }
}
