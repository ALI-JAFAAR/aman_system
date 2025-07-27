<?php

namespace Database\Factories;

use App\Models\BankAccount;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class BankAccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BankAccount::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'owner_id' => fake()->text(255),
            'owner_type' => fake()->text(255),
            'bank_name' => fake()->text(255),
            'branch_name' => fake()->text(255),
            'account_name' => fake()->text(255),
            'account_number' => fake()->bankAccountNumber(),
            'iban' => fake()->iban(),
            'currency' => fake()->currencyCode(),
            'is_primary' => fake()->boolean(),
            'deleted_at' => fake()->dateTime(),
        ];
    }
}
