<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\ProfessionalRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfessionalRecordFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProfessionalRecord::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(10),
            'organization' => fake()->text(255),
            'start_date' => fake()->date(),
            'end_date' => fake()->date(),
            'details' => fake()->sentence(20),
            'deleted_at' => fake()->dateTime(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
