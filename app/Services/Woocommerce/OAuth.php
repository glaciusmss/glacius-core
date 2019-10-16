<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/19/2019
 * Time: 12:06 PM.
 */

namespace App\Services\Woocommerce;


use App\Contracts\OAuth as OAuthContract;
use App\Contracts\SdkFactory;
use App\Contracts\Webhook as WebhookContract;
use App\Enums\MarketplaceEnum;
use App\Events\OAuthConnected;
use App\Events\OAuthDisconnected;
use App\Services\BaseMarketplace;
use Automattic\WooCommerce\Client;
use Illuminate\Contracts\Cache\Repository as CacheContract;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OAuth extends BaseMarketplace implements OAuthContract
{
    protected $sdkFactory;
    protected $webhook;

    public function __construct($config, CacheContract $cache, SdkFactory $sdkFactory, WebhookContract $webhook)
    {
        parent::__construct($config, $cache);

        $this->sdkFactory = $sdkFactory;
        $this->webhook = $webhook;
    }

    public function createAuth()
    {
        $generatedSession = Str::orderedUuid()->toString();
        $this->cache->put($this->name() . ':' . $generatedSession . ':shop', $this->getShop());
        $this->cache->put($this->name() . ':' . $generatedSession . ':woocommerce_store_url', $this->getWoocommerceStoreUrl());

        return $this->getWoocommerceStoreUrl() . '/wc-auth/v1/authorize'
            . '?app_name=GlaciusMSS.' . $generatedSession
            . '&scope=read_write'
            . '&user_id=' . $generatedSession
            . '&return_url=' . $this->config['return_url']
            . '&callback_url=' . $this->config['callback_url'];
    }

    public function oAuthCallback(Request $request)
    {
        $this->shop = throw_unless(
            $this->cache->get($this->name() . ':' . $request->input('user_id') . ':shop'),
            NotFoundHttpException::class,
            'please try again'
        );

        $woocommerceStoreUrl = throw_unless(
            $this->cache->get($this->name() . ':' . $request->input('user_id') . ':woocommerce_store_url'),
            NotFoundHttpException::class,
            'please try again'
        );

        $this->sdkFactory->setupSdk(null, [
            'url' => $woocommerceStoreUrl,
            'consumer_key' => $request->input('consumer_key'),
            'consumer_secret' => $request->input('consumer_secret'),
        ]);

        //register webhooks
        $webhookId = $this->webhook->register();

        $this->getShop()->marketplaces()->detach($this->getMarketplace()->id);
        $this->getShop()->marketplaces()->attach($this->getMarketplace()->id, [
            'meta->key' => $request->input('consumer_key'),
            'meta->secret' => $request->input('consumer_secret'),
            'meta->woocommerce_store_url' => $woocommerceStoreUrl,
            'meta->webhook_id' => $webhookId,
            'meta->session_id' => $request->input('user_id'),
        ]);

        $this->cache->forget($this->name() . ':' . $request->input('user_id') . ':shop');
        $this->cache->forget($this->name() . ':' . $request->input('user_id') . ':woocommerce_store_url');

        event(new OAuthConnected($this->getShop(), $this->name()));
    }

    public function deleteAuth()
    {
        /** @var \App\Marketplace $marketplace */
        $marketplace = $this->getShop('marketplaces')
            ->marketplaces()
            ->wherePivot('marketplace_id', '=', $this->getMarketplace()->id)
            ->first();

        $this->sdkFactory->setupSdk(null, [
            'url' => $marketplace->pivot->meta['woocommerce_store_url'],
            'consumer_key' => $marketplace->pivot->meta['key'],
            'consumer_secret' => $marketplace->pivot->meta['secret'],
        ]);

        /** @var Client $sdk */
        $sdk = $this->sdkFactory->getSdk();

        $sdk->post('webhooks/batch', [
            'delete' => $marketplace->pivot->meta['webhook_id']
        ]);

        $this->getShop()->marketplaces()->detach($this->getMarketplace()->id);

        event(new OAuthDisconnected($this->getShop(), $this->name()));
    }

    public function name()
    {
        return MarketplaceEnum::WooCommerce();
    }

    protected function getWoocommerceStoreUrl()
    {
        return $this->sdkFactory->getConfig()['url'];
    }
}
