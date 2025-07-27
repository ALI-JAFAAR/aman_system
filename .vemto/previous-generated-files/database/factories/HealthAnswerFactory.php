<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\HealthAnswer;
use Illuminate\Database\Eloquent\Factories\Factory;

class HealthAnswerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = HealthAnswer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'question_key' => fake()->text(255),
            'answer' => 'نعم',
            'deleted_at' => fake()->dateTime(),
            'user_service_id' => \App\Models\UserService::factory(),
        ];
    }
}
