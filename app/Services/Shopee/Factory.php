<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/19/2019
 * Time: 12:08 PM.
 */

namespace App\Services\Shopee;


use App\Contracts\SdkFactory;
use Illuminate\Support\Arr;

class Factory implements SdkFactory
{
    protected $sdk;
    protected $config;

    public function setupSdk(array $config = null, array $extras = [])
    {
        if (!$this->config) {
            $config = $config ?? config('marketplace.shopee');

            $this->config = [
                'partner_id' => (int)Arr::pull($config, 'id'),
                'secret' => Arr::pull($config, 'key'),
            ];
        }

        if (Arr::has($extras, 'shopeeShopId')) {
            array_merge($config, ['shopee_shop_id' => $extras['shopeeShopId']]);
            array_merge($this->config, ['shopid' => $config['shopee_shop_id']]);
        }

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
        return $this->sdk = new \Shopee\Client($this->config);
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function clearCache()
    {
        $this->sdk = null;
    }
}
