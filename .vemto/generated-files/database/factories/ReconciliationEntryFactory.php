<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\ReconciliationEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReconciliationEntryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ReconciliationEntry::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'deleted_at' => fake()->dateTime(),
            'reconciliation_id' => \App\Models\Reconciliation::factory(),
            'ledger_entry_id' => \App\Models\LedgerEntry::factory(),
        ];
    }
}
