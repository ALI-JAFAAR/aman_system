<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\Reconciliation;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReconciliationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Reconciliation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'period_start' => fake()->dateTime(),
            'period_end' => fake()->date(),
            'total_gross_amount' => fake()->randomNumber(1),
            'total_platform_share' => fake()->randomNumber(1),
            'total_organization_share' => fake()->randomNumber(1),
            'total_partner_share' => fake()->randomNumber(1),
            'status' => fake()->word(),
            'platform_reconciled_at' => fake()->dateTime(),
            'deleted_at' => fake()->dateTime(),
            'organization_id' => \App\Models\Organization::factory(),
            'contract_id' => \App\Models\Contract::factory(),
            'platform_reconciled_by' => \App\Models\Employee::factory(),
            'partner_reconciled_by' => \App\Models\Employee::factory(),
        ];
    }
}
