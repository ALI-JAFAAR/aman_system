<?php

namespace Database\Seeders;

use App\Models\Reconciliation;
use Illuminate\Database\Seeder;

class ReconciliationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Reconciliation::factory()
            ->count(5)
            ->create();
    }
}
