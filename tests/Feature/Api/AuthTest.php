<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_and_me(): void
    {
        $user = User::factory()->create([
            'password' => 'secret123',
        ]);

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ])->assertOk();

        $token = $login->json('data.token');
        $this->assertNotEmpty($token);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('data.user.id', $user->id);
    }
}

