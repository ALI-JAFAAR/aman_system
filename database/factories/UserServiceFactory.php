<?php

namespace Database\Factories;

use App\Models\UserService;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserServiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserService::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'form_data' => [],
            'status' => fake()->word(),
            'response_data' => [],
            'submitted_at' => fake()->text(255),
            'processed_at' => fake()->text(255),
            'processed_by' => fake()->randomNumber(),
            'notes' => fake()->text(),
            'deleted_at' => fake()->dateTime(),
            'user_id' => \App\Models\User::factory(),
            'service_id' => \App\Models\Service::factory(),
            'employee_id' => \App\Models\Employee::factory(),
        ];
    }
}
