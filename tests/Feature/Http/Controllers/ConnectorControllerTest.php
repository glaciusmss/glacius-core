<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\TokenType;
use App\Http\Middleware\ValidateWebhook;
use App\Http\Requests\ConnectorRequest;
use App\Services\Connectors\ConnectorManager;
use App\Models\Shop;
use App\Models\Token;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Tests\TestCase;

class ConnectorControllerTest extends TestCase
{
    use WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()
            ->has(Shop::factory())
            ->create();

        $this->actingAs($this->user);
    }

    public function testCreateOAuthSuccess()
    {
        $connectorManagerMock = \Mockery::spy(ConnectorManager::class)->makePartial();
        $connectorManagerMock->shouldReceive('processOAuth')
            ->with('shopify', 'onInstall', \Mockery::type(ConnectorRequest::class))
            ->once();

        $this->swap(ConnectorManager::class, $connectorManagerMock);

        $response = $this->postJson('/shopify/oauth', [
            'shopify_shop' => 'test',
            'rtn_url' => $this->faker->url,
            'shop_id' => $this->user->shops->first()->id,
        ]);

        $response->assertOk();
    }

    public function testWoocommerceCallbackSuccess()
    {
        $this->logout($this->user);

        $connectorManagerMock = \Mockery::spy(ConnectorManager::class)->makePartial();
        $connectorManagerMock->shouldReceive('processOAuth')
            ->with('woocommerce', 'onInstallCallback', \Mockery::type(ConnectorRequest::class))
            ->once()
            ->andReturn($this->user->shops->first());

        $this->swap(ConnectorManager::class, $connectorManagerMock);

        $response = $this->postJson('/woocommerce/callback');

        $response->assertOk();
    }

    public function testOAuthCallbackSuccess()
    {
        $this->logout($this->user);

        $connectorManagerMock = \Mockery::spy(ConnectorManager::class)->makePartial();
        $connectorManagerMock->shouldReceive('processOAuth')
            ->with('shopify', 'onInstallCallback', \Mockery::type(ConnectorRequest::class))
            ->once()
            ->andReturn($this->user->shops->first());

        $this->swap(ConnectorManager::class, $connectorManagerMock);

        $urlToBeRedirect = $this->faker->url;

        // oauth callback will use cache to check for redirect url
        \Cache::put('shopify:'.$this->user->shops->first()->id.':rtn_url', $urlToBeRedirect);

        $response = $this->getJson('/shopify/oauth');

        $response->assertRedirect($urlToBeRedirect);
    }

    public function testDeleteSuccess()
    {
        $connectorManagerMock = \Mockery::spy(ConnectorManager::class)->makePartial();
        $connectorManagerMock->shouldReceive('processOAuth')
            ->with('shopify', 'onDeleteAuth', \Mockery::type(ConnectorRequest::class))
            ->once();

        $this->swap(ConnectorManager::class, $connectorManagerMock);

        $response = $this->deleteJson('/shopify/oauth');

        $response->assertNoContent();
    }

    public function testWoocommerceRedirectSuccess()
    {
        $urlToBeReRedirectdirect = $this->faker->url;

        // create token for woocommerce redirect
        $token = Token::generateAndSave(TokenType::WoocommerceConnect(), ['id' => 1]);

        \Cache::put('woocommerce:1:rtn_url', $urlToBeReRedirectdirect);

        $response = $this->getJson('/woocommerce/redirect?'.http_build_query([
            'user_id' => $token->token,
        ])
        );

        $response->assertRedirect($urlToBeReRedirectdirect);
    }

    public function testAcceptWebhookSuccess()
    {
        $connectorManagerMock = \Mockery::spy(ConnectorManager::class)->makePartial();
        $connectorManagerMock->shouldReceive('dispatchWebhookToProcessor')
            ->with('shopify', \Mockery::type(Request::class))
            ->once();

        $this->swap(ConnectorManager::class, $connectorManagerMock);

        $this->withoutMiddleware(ValidateWebhook::class);

        $response = $this->postJson('/shopify/webhooks');

        $response->assertOk();
    }
}
