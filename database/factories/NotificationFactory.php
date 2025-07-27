<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Notification::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => fake()->word(),
            'notifiable_type' => fake()->text(255),
            'notifiable_id' => fake()->text(255),
            'data' => [],
            'read_at' => fake()->dateTime(),
            'deleted_at' => fake()->dateTime(),
        ];
    }
}
