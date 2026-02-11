<?php

namespace Database\Factories;

use App\Models\Wallet;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class WalletFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Wallet::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'balance' => fake()->randomFloat(2, 0, 9999),
            'currency' => fake()->currencyCode(),
            'walletable_type' => fake()->text(255),
            'walletable_id' => fake()->randomNumber(),
            'deleted_at' => null,
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
