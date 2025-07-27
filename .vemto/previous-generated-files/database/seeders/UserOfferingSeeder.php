<?php

namespace Database\Seeders;

use App\Models\UserOffering;
use Illuminate\Database\Seeder;

class UserOfferingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserOffering::factory()
            ->count(5)
            ->create();
    }
}
