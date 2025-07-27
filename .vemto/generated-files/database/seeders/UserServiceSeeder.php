<?php

namespace Database\Seeders;

use App\Models\UserService;
use Illuminate\Database\Seeder;

class UserServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserService::factory()
            ->count(5)
            ->create();
    }
}
