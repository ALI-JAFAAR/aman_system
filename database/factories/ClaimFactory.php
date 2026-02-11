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
            'type' => fake()->randomElement(['health', 'legal', 'financial']),
            'details' => fake()->sentence(20),
            'accident_date' => fake()->optional()->date(),
            'amount_requested' => fake()->numberBetween(1, 5000000),
            'status' => fake()->randomElement(['pending', 'needs_info', 'approved', 'rejected']),
            'resolution_amount' => 0,
            'resolution_note' => null,
            'submitted_at' => now()->toDateTimeString(),
            'deleted_at' => null,
            'user_offering_id' => \App\Models\UserOffering::factory(),
        ];
    }
}
