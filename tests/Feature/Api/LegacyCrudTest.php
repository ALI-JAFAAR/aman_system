<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LegacyCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_services_via_legacy_endpoint(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Service::factory()->create([
            'name' => 'Test Service',
            'code' => 'test_service_0001',
        ]);

        $this->getJson('/api/v1/Service')
            ->assertOk()
            ->assertJsonPath('success', true);
    }
}

