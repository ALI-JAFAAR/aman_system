<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Organization::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'type' => fake()->word(),
            'details' => fake()->sentence(20),
            'code' => fake()
                ->unique->unique()
                ->regexify('[A-Z]{3}[0-9]{3}'),
            'next_identity_sequence' => fake()->randomNumber(),
            'deleted_at' => fake()->dateTime(),
            'organization_id' => function () {
                return \App\Models\Organization::factory()->create([
                    'organization_id' => null,
                ])->id;
            },
        ];
    }
}
