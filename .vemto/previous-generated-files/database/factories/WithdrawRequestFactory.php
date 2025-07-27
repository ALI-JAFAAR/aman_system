<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\WithdrawRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class WithdrawRequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WithdrawRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount' => fake()->randomFloat(2, 0, 9999),
            'status' => fake()->word(),
            'requested_at' => fake()->dateTime(),
            'approved_at' => fake()->dateTime(),
            'notes' => fake()->text(),
            'executed_at' => fake()->dateTime(),
            'deleted_at' => fake()->dateTime(),
            'wallet_id' => \App\Models\Wallet::factory(),
            'approved_by' => \App\Models\Employee::factory(),
        ];
    }
}
