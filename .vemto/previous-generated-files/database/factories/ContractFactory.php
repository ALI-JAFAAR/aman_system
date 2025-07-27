<?php

namespace Database\Factories;

use App\Models\Contract;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContractFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Contract::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_type' => 'identity_issue',
            'initiator_type' => 'platform',
            'platform_rate' => fake()->randomNumber(1),
            'organization_rate' => fake()->randomNumber(1),
            'partner_rate' => fake()->randomNumber(1),
            'contract_start' => fake()->date(),
            'contract_end' => fake()->date(),
            'notes' => fake()->text(),
            'deleted_at' => fake()->dateTime(),
            'platform_share' => fake()->randomNumber(1),
            'organization_share' => fake()->randomNumber(1),
            'partner_share' => fake()->randomNumber(1),
            'contract_version' => fake()->randomNumber(0),
            'organization_id' => \App\Models\Organization::factory(),
            'partner_offering_id' => \App\Models\PartnerOffering::factory(),
        ];
    }
}
