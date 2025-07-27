<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\AdministrativeRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdministrativeRecordFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AdministrativeRecord::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'record_type' => 'identity',
            'title' => fake()->sentence(10),
            'description' => fake()->sentence(15),
            'record_date' => fake()->date(),
            'expiry_date' => fake()->date(),
            'record_data' => [],
            'deleted_at' => fake()->dateTime(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
