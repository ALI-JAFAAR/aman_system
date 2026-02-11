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
            'status' => fake()->randomElement(['pending', 'active', 'rejected']),
            'platform_generated_number' => fake()->optional()->bothify('AMAN-#######'),
            'partner_filled_number' => fake()->optional()->bothify('PARTNER-#######'),
            'applied_at' => now()->toDateTimeString(),
            'activated_at' => now()->toDateTimeString(),
            'rejected_at' => now()->toDateTimeString(),
            'notes' => fake()->sentence(12),
            'deleted_at' => null,
            'user_id' => \App\Models\User::factory(),
            'partner_offering_id' => \App\Models\PartnerOffering::factory(),
        ];
    }
}
