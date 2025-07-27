<?php

namespace Database\Factories;

use App\Models\Claim;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClaimFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Claim::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => fake()->word(),
            'details' => fake()->sentence(20),
            'accident_date' => fake()->date(),
            'amount_requested' => fake()->randomNumber(),
            'status' => fake()->word(),
            'resolution_amount' => fake()->randomNumber(),
            'resolution_note' => fake()->text(),
            'submitted_at' => fake()->text(255),
            'deleted_at' => fake()->dateTime(),
            'user_offering_id' => \App\Models\UserOffering::factory(),
        ];
    }
}
