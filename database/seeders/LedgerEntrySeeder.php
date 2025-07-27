<?php

namespace Database\Seeders;

use App\Models\LedgerEntry;
use Illuminate\Database\Seeder;

class LedgerEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LedgerEntry::factory()
            ->count(5)
            ->create();
    }
}
