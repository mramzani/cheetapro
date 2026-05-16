<?php

namespace Tests\Feature;

use App\Models\Seller;
use App\Models\Setting;
use App\Models\WalletTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SellerClientCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_cannot_create_client_without_enough_balance(): void
    {
        $seller = $this->sellerWithBalance(500);
        Setting::create([
            'xui_base_url' => 'https://dash.cheeta.site:2053',
            'subscription_base_url' => 'https://sub.cheeta.site:2096/sub',
            'xui_username' => 'danio',
            'xui_password' => 'danio',
            'xui_inbound_id' => 23,
            'price_per_gb' => 1000,
            'default_expiry_days' => 30,
        ]);

        $response = $this->actingAs($seller, 'seller')->post(route('seller.clients.store'), [
            'total_gb' => 1,
            'expiry_days' => 30,
        ]);

        $response->assertSessionHasErrors('client');
        $this->assertDatabaseCount('clients', 0);
        $this->assertSame(500, $seller->wallet()->first()->balance);
    }

    public function test_seller_can_create_client_and_wallet_is_deducted(): void
    {
        Http::fake([
            '*/dash/login' => Http::response(['success' => true], 200, [
                'Set-Cookie' => '3x-ui=fake-session; Path=/; HttpOnly',
            ]),
            '*/dash/panel/api/inbounds/list' => Http::response([
                'success' => true,
                'obj' => [[
                    'id' => 23,
                    'remark' => 'CheetaNet2',
                    'port' => 2052,
                    'protocol' => 'vless',
                    'settings' => json_encode(['encryption' => 'none']),
                    'streamSettings' => json_encode([
                        'network' => 'ws',
                        'security' => 'none',
                        'externalProxy' => [[
                            'dest' => 'anten.ir',
                            'port' => 2052,
                        ]],
                        'wsSettings' => [
                            'path' => '/',
                            'host' => 'iran.top10hub.ir',
                        ],
                    ]),
                ]],
            ]),
            '*/dash/panel/api/inbounds/addClient' => Http::response(['success' => true]),
        ]);

        $seller = $this->sellerWithBalance(5000);
        Setting::create([
            'xui_base_url' => 'https://dash.cheeta.site:2053',
            'subscription_base_url' => 'https://sub.cheeta.site:2096/sub',
            'xui_username' => 'danio',
            'xui_password' => 'danio',
            'xui_inbound_id' => 23,
            'price_per_gb' => 1000,
            'default_expiry_days' => 30,
        ]);

        $response = $this->actingAs($seller, 'seller')->post(route('seller.clients.store'), [
            'total_gb' => 2,
            'expiry_days' => 30,
            'comment' => 'یه یادداشت',
        ]);

        $client = $seller->clients()->first();

        $response->assertRedirect(route('seller.clients.show', $client));
        $this->assertSame(3000, $seller->wallet()->first()->balance);
        $this->assertDatabaseHas('clients', [
            'seller_id' => $seller->id,
            'email' => 'mohamad2G101',
            'inbound_id' => 23,
            'total_gb' => 2,
            'cost' => 2000,
        ]);
        $this->assertNotNull($client->sub_id);
        $this->assertStringStartsWith('vless://'.$client->uuid.'@anten.ir:2052', $client->config_link);
        $this->assertSame('https://sub.cheeta.site:2096/sub/'.$client->sub_id, $client->subscription_link);
        $this->assertDatabaseHas('wallet_transactions', [
            'seller_id' => $seller->id,
            'type' => WalletTransaction::TYPE_CLIENT_PURCHASE,
            'amount' => 2000,
            'balance_after' => 3000,
        ]);
    }

    private function sellerWithBalance(int $balance): Seller
    {
        $seller = Seller::create([
            'name' => 'Mohamad',
            'username' => 'mohamad',
            'password' => 'secret123',
        ]);
        $seller->wallet()->create(['balance' => $balance]);

        return $seller;
    }
}
