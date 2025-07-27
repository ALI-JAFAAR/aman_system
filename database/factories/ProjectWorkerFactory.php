<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\ProjectWorker;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectWorkerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProjectWorker::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'role' => fake()->word(),
            'assigned_at' => fake()->date(),
            'active' => fake()->boolean(),
            'notes' => fake()->text(),
            'deleted_at' => fake()->dateTime(),
            'project_id' => \App\Models\Project::factory(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
