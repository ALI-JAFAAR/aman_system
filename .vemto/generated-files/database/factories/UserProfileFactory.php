<?php

namespace Database\Factories;

use App\Models\UserProfile;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'mother_name' => fake()->text(255),
            'national_id' => fake()->text(255),
            'date_of_birth' => fake()->text(255),
            'place_of_birth' => fake()->text(255),
            'phone' => fake()->phoneNumber(),
            'address_province' => fake()->text(255),
            'address_district' => fake()->text(255),
            'address_subdistrict' => fake()->text(255),
            'address_details' => fake()->text(255),
            'extra_data' => [],
            'deleted_at' => fake()->dateTime(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
