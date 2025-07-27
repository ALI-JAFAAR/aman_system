<?php

namespace Database\Factories;

use App\Models\LedgerEntry;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class LedgerEntryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LedgerEntry::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reference_type' => fake()->text(255),
            'reference_id' => fake()->randomNumber(),
            'account_code' => fake()->text(255),
            'entry_type' => 'debit',
            'amount' => fake()->randomFloat(2, 0, 9999),
            'description' => fake()->sentence(15),
            'is_locked' => fake()->boolean(),
            'deleted_at' => fake()->dateTime(),
            'created_by' => \App\Models\Employee::factory(),
        ];
    }
}
