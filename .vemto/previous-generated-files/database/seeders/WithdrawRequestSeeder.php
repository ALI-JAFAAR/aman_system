<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WithdrawRequest;

class WithdrawRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WithdrawRequest::factory()
            ->count(5)
            ->create();
    }
}
