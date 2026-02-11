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
            'status' => 'completed',
            'reference_type' => null,
            'reference_id' => 0,
            'description' => fake()->sentence(15),
            'deleted_at' => null,
            'wallet_id' => \App\Models\Wallet::factory(),
            'target_wallet_id' => null,
        ];
    }
}
