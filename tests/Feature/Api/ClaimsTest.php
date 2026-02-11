<?php

namespace Tests\Feature\Api;

use App\Models\Claim;
use App\Models\User;
use App\Models\UserOffering;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ClaimsTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_and_list_claims(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $uo = UserOffering::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->postJson('/api/v1/claims', [
            'user_offering_id' => $uo->id,
            'type' => 'health',
            'amount_requested' => 1000,
            'details' => 'test',
        ])->assertStatus(201);

        $this->getJson('/api/v1/claims')
            ->assertOk()
            ->assertJsonPath('data.data.0.type', 'health');
    }

    public function test_can_approve_claim(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $claim = Claim::factory()->create([
            'status' => 'pending',
        ]);

        $this->postJson("/api/v1/claims/{$claim->id}/approve", [
            'resolution_amount' => 500,
            'resolution_note' => 'ok',
        ])->assertOk()
            ->assertJsonPath('data.status', 'approved');
    }
}

