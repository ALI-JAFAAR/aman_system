<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\PartnerOffering;
use Illuminate\Database\Eloquent\Factories\Factory;

class PartnerOfferingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PartnerOffering::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'price' => fake()->randomFloat(2, 0, 9999),
            'contract_start' => fake()->date(),
            'contract_end' => fake()->date(),
            'auto_approve' => fake()->boolean(),
            'partner_must_fill_number' => fake()->boolean(),
            'deleted_at' => fake()->dateTime(),
            'organization_id' => \App\Models\Organization::factory(),
            'package_id' => \App\Models\Package::factory(),
        ];
    }
}
