<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\UserOffering;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserOfferingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserOffering::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'status' => fake()->word(),
            'platform_generated_number' => fake()->text(255),
            'partner_filled_number' => fake()->text(255),
            'applied_at' => fake()->text(255),
            'activated_at' => fake()->text(255),
            'rejected_at' => fake()->text(255),
            'notes' => fake()->text(),
            'deleted_at' => fake()->dateTime(),
            'user_id' => \App\Models\User::factory(),
            'partner_offering_id' => \App\Models\PartnerOffering::factory(),
        ];
    }
}
