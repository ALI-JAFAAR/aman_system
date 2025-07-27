<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Service::class;

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
            'form_schema' => [],
            'deleted_at' => fake()->dateTime(),
        ];
    }
}
