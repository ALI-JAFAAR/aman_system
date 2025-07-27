<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserAffiliation;

class UserAffiliationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserAffiliation::factory()
            ->count(5)
            ->create();
    }
}
