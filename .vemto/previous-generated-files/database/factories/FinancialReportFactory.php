<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\FinancialReport;
use Illuminate\Database\Eloquent\Factories\Factory;

class FinancialReportFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FinancialReport::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(10),
            'report_type' => 'balance_sheet',
            'parameters' => [],
            'file_path' => fake()->text(255),
            'generated_at' => fake()->dateTime(),
            'notes' => fake()->text(),
            'deleted_at' => fake()->dateTime(),
            'generated_by' => \App\Models\Employee::factory(),
        ];
    }
}
