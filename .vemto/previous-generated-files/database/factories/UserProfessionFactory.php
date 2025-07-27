<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\UserProfession;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserProfessionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserProfession::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'status' => fake()->word(),
            'applied_at' => fake()->date(),
            'approved_at' => fake()->dateTime(),
            'notes' => fake()->text(),
            'deleted_at' => fake()->dateTime(),
            'user_affiliation_id' => \App\Models\UserAffiliation::factory(),
            'profession_id' => \App\Models\Profession::factory(),
            'specialization_id' => \App\Models\Specialization::factory(),
            'approved_by' => \App\Models\Employee::factory(),
        ];
    }
}
