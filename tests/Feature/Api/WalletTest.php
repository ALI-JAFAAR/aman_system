<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WalletTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_transfer_between_wallets(): void
    {
        $fromUser = User::factory()->create();
        $toUser = User::factory()->create();

        $fromWallet = Wallet::factory()->create([
            'walletable_type' => User::class,
            'walletable_id' => $fromUser->id,
            'user_id' => $fromUser->id,
            'currency' => 'IQD',
            'balance' => 1000,
        ]);

        $toWallet = Wallet::factory()->create([
            'walletable_type' => User::class,
            'walletable_id' => $toUser->id,
            'user_id' => $toUser->id,
            'currency' => 'IQD',
            'balance' => 0,
        ]);

        Sanctum::actingAs($fromUser);

        $this->postJson('/api/v1/wallet/transfers', [
            'to_wallet_id' => $toWallet->id,
            'amount' => 250,
        ])->assertStatus(201);

        $this->assertEquals(750, (float) $fromWallet->fresh()->balance);
        $this->assertEquals(250, (float) $toWallet->fresh()->balance);
    }
}

