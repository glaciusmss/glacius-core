<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/19/2019
 * Time: 12:06 PM.
 */

namespace App\Services\Easystore;


use App\Contracts\OAuth as OAuthContract;
use App\Contracts\SdkFactory;
use App\Contracts\Webhook as WebhookContract;
use App\Enums\MarketplaceEnum;
use App\Events\OAuthConnected;
use App\Events\OAuthDisconnected;
use App\Services\BaseMarketplace;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Illuminate\Http\Request;
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
        $this->cache->put($this->name() . ':' . $this->getEasystoreShop() . ':shop', $this->getShop());

        $this->sdkFactory->setupSdk(null, [
            'scopes' => 'read_orders,read_customers,read_products,write_products',
            'redirect_uri' => $this->getConfig('redirect_url')
        ]);

        /** @var \EasyStore\Client $sdk */
        $sdk = $this->sdkFactory->getSdk();

        return $sdk->buildAuthUrl();
    }

    public function oAuthCallback(Request $request)
    {
        $this->shop = throw_unless(
            $this->cache->get($this->name() . ':' . $this->getEasystoreShop() . ':shop'),
            NotFoundHttpException::class,
            'please try again'
        );

        /** @var \EasyStore\Client $sdk */
        $sdk = $this->sdkFactory->getSdk();

        $accessToken = $sdk->getAccessToken();

        $this->sdkFactory->setupSdk(null, [
            'access_token' => $accessToken
        ]);

        //register webhooks
        $webhookId = $this->webhook->register();

        $this->getShop()->marketplaces()->detach($this->getMarketplace()->id);
        $this->getShop()->marketplaces()->attach($this->getMarketplace()->id, [
            'token' => $accessToken,
            'meta->easystore_shop' => $this->getEasystoreShop(),
            'meta->webhook_id' => $webhookId,
        ]);

        $this->cache->forget($this->name() . ':' . $this->getEasystoreShop() . ':shop');

        event(new OAuthConnected($this->getShop(), $this->name()));
    }

    public function deleteAuth()
    {
        $marketplace = $this->getShop('marketplaces')
            ->marketplaces()
            ->wherePivot('marketplace_id', '=', $this->getMarketplace()->id)
            ->first();

        $this->sdkFactory->setupSdk(null, [
            'access_token' => $marketplace->pivot->token,
            'shop' => $marketplace->pivot->meta['easystore_shop'],
        ]);

        /** @var \Easystore\Client $sdk */
        $sdk = $this->sdkFactory->getSdk();

        foreach ($marketplace->pivot->meta['webhook_id'] as $webhookId) {
            $sdk->delete("/webhooks/{$webhookId}.json");
        }

        $this->getShop()->marketplaces()->detach($this->getMarketplace()->id);

        event(new OAuthDisconnected($this->getShop(), $this->name()));
    }

    public function name()
    {
        return MarketplaceEnum::EasyStore();
    }

    protected function getEasystoreShop()
    {
        return $this->sdkFactory->getConfig()['shop'];
    }
}
