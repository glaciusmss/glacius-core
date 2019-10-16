<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/19/2019
 * Time: 12:08 PM.
 */

namespace App\Services\Shopify;


use App\Contracts\SdkFactory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use PHPShopify\ShopifySDK;

class Factory implements SdkFactory
{
    protected $sdk;
    protected $config;

    public function setupSdk(array $config = null, array $extras = [])
    {
        if (!$this->config) {
            $this->config = $config ?? config('marketplace.shopify');
        }

        if (Arr::has($extras, 'shopifyShop')) {
            $shopifyShop = Arr::pull($extras, 'shopifyShop');
            $this->setShopifyShop($shopifyShop);
        }

        //merge all extra config to config
        $this->config = array_merge($this->config, $extras);

        $this->setConfigToSdk();

        $this->clearCache();
    }

    public function getSdk()
    {
        if ($this->sdk) {
            return $this->sdk;
        }

        return $this->makeSdk();
    }

    public function makeSdk()
    {
        return $this->sdk = new ShopifySDK();
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function clearCache()
    {
        $this->sdk = null;
    }

    protected function setConfigToSdk()
    {
        $config = collect($this->config);

        $sdkConfig = [
            'ApiKey' => $config->pull('key'),
            'SharedSecret' => $config->pull('secret'),
        ];

        $config->when($config->has('shop_url'), function ($collection) use (&$sdkConfig) {
            $sdkConfig['ShopUrl'] = $collection->pull('shop_url');
        });

        ShopifySDK::config(
            array_merge($sdkConfig, $config->toArray())
        );
    }

    protected function setShopifyShop($shopifyShop)
    {
        if (Str::endsWith($shopifyShop, '/')) {
            $shopifyShop = substr($shopifyShop, 0, -1);
        }

        if (!Str::contains($shopifyShop, '.myshopify.com')) {
            $shopifyShop .= '.myshopify.com';
        }

        $this->config = array_merge($this->config, ['shop_url' => $shopifyShop]);

        return $this;
    }
}
