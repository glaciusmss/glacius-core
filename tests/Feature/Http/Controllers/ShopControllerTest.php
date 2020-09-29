<?php


namespace Tests\Feature\Http\Controllers;


use App\Shop;
use App\User;
use Tests\TestCase;

class ShopControllerTest extends TestCase
{
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()
            ->create();

        $this->actingAs($this->user);
    }

    public function testGetShopSuccess()
    {
        $shop = Shop::factory()->create();
        $this->user->shops()->attach($shop);

        $response = $this->getJson('/shop');

        $response->assertOk()->assertJsonCount(1);
        $this->assertArrayHasKey('name', $response->decodeResponseJson()[0]);
        $this->assertArrayHasKey('description', $response->decodeResponseJson()[0]);
    }

    public function testCreateShopSuccess()
    {
        $response = $this->postJson('/shop', [
            'name' => 'test_shop',
            'description' => 'test_desc'
        ]);

        $response->assertCreated()
            ->assertJsonFragment([
                'name' => 'test_shop',
                'description' => 'test_desc'
            ]);

        $this->assertDatabaseHas('shops', [
            'name' => 'test_shop',
            'description' => 'test_desc'
        ]);
    }

    public function testGetSingleShopSuccess()
    {
        $shop = Shop::factory()->create();
        $this->user->shops()->attach($shop);

        $response = $this->getJson('/shop/' . $shop->id);

        $response->assertOk()
            ->assertJsonFragment([
                'name' => $shop->name,
                'description' => $shop->description
            ]);
    }

    public function testUpdateShopSuccess()
    {
        $shop = Shop::factory()->create();
        $this->user->shops()->attach($shop);

        // make sure that factory created name same with db name
        $this->assertEquals($shop->name, $this->user->shops->first()->name);

        $response = $this->patchJson('/shop/' . $shop->id, [
            'name' => 'test_shop'
        ]);

        $response->assertNoContent();

        $this->user->unsetRelations();

        $this->assertEquals('test_shop', $this->user->shops->first()->name);
    }

    public function testDeleteShopSuccess()
    {
        $shop = Shop::factory()->create();
        $this->user->shops()->attach($shop);

        $response = $this->deleteJson('/shop/' . $shop->id);

        $response->assertNoContent();

        $this->assertDeleted($shop);
    }
}
