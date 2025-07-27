<?php

namespace Database\Factories;

use App\Models\Vehicle;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Vehicle::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'plate_number' => fake()->text(255),
            'plate_code' => fake()->text(255),
            'model' => [],
            'owner_data' => [],
            'notes' => fake()->text(),
            'deleted_at' => fake()->dateTime(),
            'user_service_id' => \App\Models\UserService::factory(),
        ];
    }
}
