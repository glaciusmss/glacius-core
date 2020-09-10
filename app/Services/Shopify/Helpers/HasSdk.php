<?php


namespace App\Services\Shopify\Helpers;


use App\Exceptions\NotSupportedException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use PHPShopify\ShopifySDK;

trait HasSdk
{
    public function getSdk(array $configuration = null)
    {
        $configuration = $this->studlyCaseConfig($configuration);

        if (!Arr::hasAny($configuration, ['ShopUrl'])) {
            throw new NotSupportedException('some configuration missing');
        }

        return new ShopifySDK(array_merge([
            'ApiKey' => config('marketplace.shopify.key'),
            'SharedSecret' => config('marketplace.shopify.secret'),
        ], $configuration));
    }

    public function configureSdk(array $configuration = null)
    {
        ShopifySDK::config(array_merge([
            'ApiKey' => config('marketplace.shopify.key'),
            'SharedSecret' => config('marketplace.shopify.secret'),
        ], $configuration));
    }

    protected function studlyCaseConfig(array $configuration): array
    {
        $studlyCaseFunction = static function ($key) {
            return Str::studly($key);
        };

        return array_combine(
            array_map($studlyCaseFunction, array_keys($configuration)),
            array_values($configuration)
        );
    }
}
