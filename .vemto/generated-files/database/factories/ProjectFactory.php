<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Project::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'description' => fake()->sentence(15),
            'location' => fake()->text(),
            'start_date' => fake()->date(),
            'end_date' => fake()->date(),
            'status' => fake()->word(),
            'deleted_at' => fake()->dateTime(),
            'owner_id' => \App\Models\User::factory(),
            'organization_id' => \App\Models\Organization::factory(),
        ];
    }
}
