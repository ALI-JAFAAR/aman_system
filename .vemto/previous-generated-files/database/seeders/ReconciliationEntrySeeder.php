<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReconciliationEntry;

class ReconciliationEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ReconciliationEntry::factory()
            ->count(5)
            ->create();
    }
}
