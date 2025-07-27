<?php

namespace Database\Seeders;

use App\Models\UserProfession;
use Illuminate\Database\Seeder;

class UserProfessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserProfession::factory()
            ->count(5)
            ->create();
    }
}
