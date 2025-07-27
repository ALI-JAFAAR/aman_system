<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\OrganizationSpecialization;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationSpecializationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OrganizationSpecialization::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'deleted_at' => fake()->dateTime(),
            'organization_id' => \App\Models\Organization::factory(),
            'specialization_id' => \App\Models\Specialization::factory(),
        ];
    }
}
