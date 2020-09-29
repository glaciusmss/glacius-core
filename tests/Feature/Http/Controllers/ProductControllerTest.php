<?php


namespace Tests\Feature\Http\Controllers;

use App\Events\Product\ProductCreated;
use App\Events\Product\ProductDeleted;
use App\Events\Product\ProductUpdated;
use App\Product;
use App\ProductVariant;
use App\Shop;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use ScoutElastic\Facades\ElasticClient;
use Tests\ElasticSearchTestingHelper;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    protected $user;
    protected $products;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()
            ->has(Shop::factory())
            ->create();

        $this->actingAs($this->user);

        /** @var Collection $orders */
        $this->products = Product::withoutSyncingToSearch(function () {
            return Product::factory()
                ->has(ProductVariant::factory())
                ->count(2)
                ->create();
        });

        $this->products->each(function (Product $product) {
            Product::withoutSyncingToSearch(function () use ($product) {
                $product->shop()->associate($this->user->shops()->first());
                $product->save();
            });
        });
    }

    public function testGetProductsSuccess()
    {
        $response = $this->getJson('/product?' . http_build_query([
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

    public function testSearchProductSuccess()
    {
        // make first order searchable
        ElasticClient::shouldReceive('index')->once();
        $this->products->first()->searchable();

        // mock order search
        ElasticClient::shouldReceive('search')
            ->once()
            ->andReturn(ElasticSearchTestingHelper::makeSearchResponse([$this->products->first()->attributesToArray()], $this->products->first()));

        $response = $this->getJson('/product?' . http_build_query([
                'shop_id' => $this->user->shops->first()->id,
                'search' => $this->products->first()->name,
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

    public function testCreateProduct()
    {
        \Event::fake();

        Product::disableSearchSyncing();

        $response = $this->postJson('/product', [
            'shop_id' => $this->user->shops->first()->id,
            'name' => 'test_product',
            'description' => 'test_description',
            'product_variants' => [
                [
                    'price' => 10.00,
                    'stock' => 10,
                ]
            ]
        ]);

        $this->assertDatabaseHas('products', [
            'name' => 'test_product',
            'description' => 'test_description',
        ]);

        $response->assertCreated()
            ->assertJsonFragment([
                'name' => 'test_product',
                'description' => 'test_description',
            ]);

        Product::enableSearchSyncing();

        \Event::assertDispatched(ProductCreated::class);
    }

    public function testShowSingledescription()
    {
        $response = $this->getJson('/product/' . $this->products->first()->id . '?' . http_build_query([
                'shop_id' => $this->user->shops->first()->id,
            ])
        );

        $response->assertOk()
            ->assertJsonFragment([
                'name' => (string)$this->products->first()->name,
                'description' => (string)$this->products->first()->description
            ]);
    }

    public function testUpdateProduct()
    {
        \Event::fake();

        Product::disableSearchSyncing();

        $response = $this->patchJson('/product/'.$this->products->first()->id, [
            'shop_id' => $this->user->shops->first()->id,
            'name' => 'test_product',
            'description' => 'test_description',
            'product_variants' => [
                [
                    'id' => $this->products->first()->productVariants->first()->id,
                    'price' => 10.00,
                    'stock' => 10,
                ]
            ]
        ]);

        $this->assertDatabaseHas('products', [
            'name' => 'test_product',
            'description' => 'test_description',
        ]);

        $response->assertNoContent();

        Product::enableSearchSyncing();

        \Event::assertDispatched(ProductUpdated::class);
    }

    public function testDeleteProduct()
    {
        \Event::fake();

        Product::disableSearchSyncing();

        $response = $this->deleteJson('/product/'.$this->products->first()->id, [
            'shop_id' => $this->user->shops->first()->id,
        ]);

        $response->assertNoContent();

        $this->assertDeleted($this->products->first());

        Product::enableSearchSyncing();

        \Event::assertDispatched(ProductDeleted::class);
    }
}
