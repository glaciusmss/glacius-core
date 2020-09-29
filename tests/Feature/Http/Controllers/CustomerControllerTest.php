<?php


namespace Tests\Feature\Http\Controllers;


use App\Address;
use App\Contact;
use App\Customer;
use App\Marketplace;
use App\Shop;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use ScoutElastic\Facades\ElasticClient;
use Tests\ElasticSearchTestingHelper;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
{
    protected $user;
    protected $customers;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()
            ->has(Shop::factory())
            ->create();

        $this->actingAs($this->user);

        /** @var Collection $customers */
        $this->customers = Customer::withoutSyncingToSearch(function () {
            return Customer::factory()
                ->for(Marketplace::factory())
                ->has(Address::factory())
                ->has(Contact::factory())
                ->count(2)
                ->create();
        });

        $this->customers->each(function (Customer $customer) {
            Customer::withoutSyncingToSearch(function () use ($customer) {
                $customer->shop()->associate($this->user->shops()->first());
                $customer->save();
            });
        });
    }

    public function testGetCustomersSuccess()
    {
        $response = $this->getJson('/customer?' . http_build_query([
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

    public function testSearchCustomerSuccess()
    {
        // make first customer searchable
        ElasticClient::shouldReceive('index')->once();
        $this->customers->first()->searchable();

        // mock customer search
        ElasticClient::shouldReceive('search')
            ->once()
            ->andReturn(ElasticSearchTestingHelper::makeSearchResponse([$this->customers->first()->attributesToArray()], $this->customers->first()));

        $response = $this->getJson('/customer?' . http_build_query([
                'shop_id' => $this->user->shops->first()->id,
                'search' => $this->customers->first()->contact->first_name
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
}
