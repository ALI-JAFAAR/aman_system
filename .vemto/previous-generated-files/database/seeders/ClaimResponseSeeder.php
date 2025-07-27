<?php

namespace Database\Seeders;

use App\Models\ClaimResponse;
use Illuminate\Database\Seeder;

class ClaimResponseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ClaimResponse::factory()
            ->count(5)
            ->create();
    }
}
