<?php


namespace Tests\Feature\Http\Controllers;

use App\Marketplace;
use App\Order;
use App\Shop;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use ScoutElastic\Facades\ElasticClient;
use Tests\ElasticSearchTestingHelper;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    protected $user;
    protected $orders;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()
            ->has(Shop::factory())
            ->create();

        $this->actingAs($this->user);

        /** @var Collection $orders */
        $this->orders = Order::withoutSyncingToSearch(function () {
            return Order::factory()
                ->for(Marketplace::factory())
                ->withoutCustomer()
                ->count(2)
                ->create();
        });

        $this->orders->each(function (Order $order) {
            Order::withoutSyncingToSearch(function () use ($order) {
                $order->shop()->associate($this->user->shops()->first());
                $order->save();
            });
        });
    }

    public function testGetOrdersSuccess()
    {
        $response = $this->getJson('/order?' . http_build_query([
                'shop_id' => $this->user->shops->first()->id
            ])
        );

        $response->assertOk()
            ->assertJsonStructure([
                'data',
                'meta',
                'links'
            ]);

        $this->assertCount(2, $response->decodeResponseJson()['data']);
    }

    public function testSearchOrderSuccess()
    {
        // make first order searchable
        ElasticClient::shouldReceive('index')->once();
        $this->orders->first()->searchable();

        // mock order search
        ElasticClient::shouldReceive('search')
            ->once()
            ->andReturn(ElasticSearchTestingHelper::makeSearchResponse([$this->orders->first()->attributesToArray()], $this->orders->first()));

        $response = $this->getJson('/order?' . http_build_query([
                'shop_id' => $this->user->shops->first()->id,
                'search' => $this->orders->first()->total_price,
            ])
        );

        $response->assertOk()
            ->assertJsonStructure([
                'data',
                'meta',
                'links'
            ]);

        $this->assertCount(1, $response->decodeResponseJson()['data']);
    }

    public function testShowSingleOrder()
    {
        $response = $this->getJson('/order/' . $this->orders->first()->id . '?' . http_build_query([
                'shop_id' => $this->user->shops->first()->id,
            ])
        );

        $response->assertOk()
            ->assertJsonFragment([
                'total_price' => (string)$this->orders->first()->total_price,
                'subtotal_price' => (string)$this->orders->first()->subtotal_price
            ]);
    }
}
