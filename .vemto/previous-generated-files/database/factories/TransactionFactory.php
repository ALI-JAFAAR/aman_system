<?php

namespace Database\Factories;

use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'transaction_type' => 'credit',
            'amount' => fake()->randomFloat(2, 0, 9999),
            'status' => fake()->word(),
            'reference_type' => fake()->text(255),
            'reference_id' => fake()->randomNumber(),
            'description' => fake()->sentence(15),
            'deleted_at' => fake()->dateTime(),
            'target_wallet_id' => \App\Models\Wallet::factory(),
            'target_wallet_id' => \App\Models\Wallet::factory(),
        ];
    }
}
