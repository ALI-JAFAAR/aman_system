<?php

namespace Database\Factories;

use App\Models\Profession;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfessionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Profession::class;

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
            'code' => fake()
                ->unique->unique()
                ->regexify('[A-Z]{3}[0-9]{3}'),
            'deleted_at' => fake()->dateTime(),
        ];
    }
}
