<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\ClaimResponse;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClaimResponseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ClaimResponse::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'action' => 'request_info',
            'actor_type' => 'employee',
            'actor_id' => fake()->randomNumber(),
            'message' => fake()->sentence(20),
            'deleted_at' => fake()->dateTime(),
            'claim_id' => \App\Models\Claim::factory(),
        ];
    }
}
