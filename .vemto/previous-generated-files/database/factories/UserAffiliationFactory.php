<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\UserAffiliation;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserAffiliationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserAffiliation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'status' => fake()->word(),
            'joined_at' => fake()->dateTime(),
            'deleted_at' => fake()->dateTime(),
            'organization_id' => \App\Models\Organization::factory(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
