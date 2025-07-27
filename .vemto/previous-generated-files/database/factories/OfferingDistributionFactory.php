<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\OfferingDistribution;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfferingDistributionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OfferingDistribution::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mode' => 'percentage',
            'value' => fake()->randomNumber(1),
            'status' => fake()->word(),
            'deleted_at' => fake()->dateTime(),
            'organization_id' => \App\Models\Organization::factory(),
            'partner_offering_id' => \App\Models\PartnerOffering::factory(),
        ];
    }
}
