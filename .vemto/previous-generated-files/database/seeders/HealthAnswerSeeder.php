<?php

namespace Database\Seeders;

use App\Models\HealthAnswer;
use Illuminate\Database\Seeder;

class HealthAnswerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        HealthAnswer::factory()
            ->count(5)
            ->create();
    }
}
