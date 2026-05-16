<?php

namespace Tests\Feature;

use App\Models\Seller;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminWalletTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_charge_seller_wallet(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $seller = Seller::create([
            'name' => 'Mohamad',
            'username' => 'mohamad',
            'password' => 'secret123',
        ]);
        $seller->wallet()->create(['balance' => 1000]);

        $response = $this->actingAs($admin)->post(route('admin.wallets.charge.store', $seller), [
            'amount' => 2500,
            'description' => 'manual charge',
        ]);

        $response->assertRedirect(route('admin.sellers.index'));
        $this->assertSame(3500, $seller->wallet()->first()->balance);
        $this->assertDatabaseHas('wallet_transactions', [
            'seller_id' => $seller->id,
            'type' => WalletTransaction::TYPE_CHARGE,
            'amount' => 2500,
            'balance_after' => 3500,
        ]);
    }
}
