<?php

namespace Database\Factories;

use App\Models\AuditLog;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuditLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AuditLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'action' => fake()->text(255),
            'model_id' => fake()->randomNumber(),
            'changes' => [],
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'deleted_at' => fake()->dateTime(),
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
