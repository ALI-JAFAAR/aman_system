<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\Specialization;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpecializationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Specialization::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()
                ->unique()
                ->regexify('[A-Z]{3}[0-9]{3}'),
            'name' => fake()->name(),
            'description' => fake()->sentence(15),
            'deleted_at' => fake()->dateTime(),
            'profession_id' => \App\Models\Profession::factory(),
        ];
    }
}
