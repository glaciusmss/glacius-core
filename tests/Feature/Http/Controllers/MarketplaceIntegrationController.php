<?php


namespace Tests\Feature\Http\Controllers;


use App\Models\Marketplace;
use App\Models\Shop;
use App\Models\User;
use Tests\TestCase;

class MarketplaceIntegrationController extends TestCase
{
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()
            ->has(
                Shop::factory()
                    ->has(Marketplace::factory())
            )
            ->create();

        $this->actingAs($this->user);
    }

    public function testGetAllIntegratedMarketplace()
    {
        $response = $this->getJson('/marketplace-integration?' . http_build_query([
                'shop_id' => $this->user->shops->first()->id
            ])
        );

        $response->assertOk()
            ->assertJsonCount(1);
    }
}
