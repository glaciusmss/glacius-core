<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/19/2019
 * Time: 12:06 PM.
 */

namespace App\Services\Shopify;


use App\Contracts\OAuth as OAuthContract;
use App\Contracts\SdkFactory;
use App\Contracts\Webhook as WebhookContract;
use App\Enums\DeviceType;
use App\Enums\MarketplaceEnum;
use App\Events\OAuthConnected;
use App\Events\OAuthDisconnected;
use App\Services\BaseMarketplace;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Illuminate\Http\Request;
use PHPShopify\AuthHelper;
use PHPShopify\ShopifySDK;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OAuth extends BaseMarketplace implements OAuthContract
{
    protected $cache;
    protected $sdkFactory;
    protected $webhook;

    public function __construct(CacheContract $cache, SdkFactory $sdkFactory, WebhookContract $webhook)
    {
        $this->cache = $cache;
        $this->sdkFactory = $sdkFactory;
        $this->webhook = $webhook;
    }

    public function createAuth()
    {
        $this->cache->put($this->name() . ':' . $this->getShopifyShop() . ':shop', $this->getShop());
        if (request()->header('x-request-from') === 'mobile') {
            $this->cache->put($this->name() . ':' . $this->getShopifyShop() . ':device', DeviceType::Mobile());
        }

        return AuthHelper::createAuthRequest(
            'read_orders,read_customers,read_products,write_products',
            $this->getConfig('redirect_url'),
            null,
            null,
            true
        );
    }

    public function oAuthCallback(Request $request)
    {
        $this->shop = throw_unless(
            $this->cache->get($this->name() . ':' . $this->getShopifyShop() . ':shop'),
            NotFoundHttpException::class,
            'please try again'
        );

        $accessToken = AuthHelper::getAccessToken();

        $this->sdkFactory->setupSdk(null, [
            'AccessToken' => $accessToken
        ]);

        //register webhooks
        $webhookId = $this->webhook->register();

        $this->getShop()->marketplaces()->detach($this->getMarketplace()->id);
        $this->getShop()->marketplaces()->attach($this->getMarketplace()->id, [
            'token' => $accessToken,
            'meta->shopify_shop' => $this->getShopifyShop(),
            'meta->webhook_id' => $webhookId,
        ]);

        $device = $this->cache->get($this->name() . ':' . $this->getShopifyShop() . ':device', DeviceType::Web());

        $this->cache->forget($this->name() . ':' . $this->getShopifyShop() . ':device');
        $this->cache->forget($this->name() . ':' . $this->getShopifyShop() . ':shop');

        event(new OAuthConnected($this->getShop(), $this->name()));

        return $device;
    }

    public function deleteAuth()
    {
        /** @var \App\Marketplace $marketplace */
        $marketplace = $this->getShop('marketplaces')
            ->marketplaces()
            ->wherePivot('marketplace_id', '=', $this->getMarketplace()->id)
            ->first();

        $this->sdkFactory->setupSdk(null, [
            'shopifyShop' => $marketplace->pivot->meta['shopify_shop']
        ]);

        try {
            $client = new Client();
            $client->delete(ShopifySDK::getAdminUrl() . 'api_permissions/current.json', [
                'headers' => [
                    'X-Shopify-Access-Token' => $marketplace->pivot->token
                ]
            ]);
        } catch (RequestException $e) {
            throw new BadRequestHttpException('unable to disconnect, please try again');
        }

        $this->getShop()->marketplaces()->detach($marketplace->id);

        event(new OAuthDisconnected($this->getShop(), $this->name()));
    }

    public function name()
    {
        return MarketplaceEnum::Shopify();
    }

    protected function getShopifyShop()
    {
        return ShopifySDK::$config['ShopUrl'];
    }
}
