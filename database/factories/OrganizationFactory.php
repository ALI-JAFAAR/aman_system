<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Organization::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            // Must match allowed DB enum values (see create_organizations_table migration)
            'type' => fake()->randomElement([
                'government_institution',
                'insurance_company',
                'law_firm',
                'trade_union',
                'sub_union',
                'general_union',
                'platform',
            ]),
            'details' => fake()->sentence(20),
            'code' => fake()
                ->unique->unique()
                ->regexify('[A-Z]{3}[0-9]{3}'),
            'next_identity_sequence' => fake()->randomNumber(),
            'deleted_at' => null,
            'organization_id' => function () {
                // Ensure there is a root platform org (id=1, parent=self) to satisfy NOT NULL + FK constraint.
                if (! Organization::query()->whereKey(1)->exists()) {
                    DB::table('organizations')->insert([
                        'id' => 1,
                        'name' => 'Platform Root',
                        'type' => 'platform',
                        'details' => 'Root organization',
                        'organization_id' => 1,
                        'code' => 'ROOT001',
                        'next_identity_sequence' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                        'deleted_at' => null,
                    ]);
                }

                return 1;
            },
        ];
    }
}
