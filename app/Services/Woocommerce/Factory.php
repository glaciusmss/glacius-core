<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/19/2019
 * Time: 12:08 PM.
 */

namespace App\Services\Woocommerce;


use App\Contracts\SdkFactory;
use Automattic\WooCommerce\Client;
use Illuminate\Support\Arr;

class Factory implements SdkFactory
{
    protected $sdk;
    protected $config;

    public function setupSdk(array $config = null, array $extras = [])
    {
        if (!$this->config) {
            $this->config = $config ?? config('marketplace.woocommerce');
        }

        //merge all extra config to config
        $this->config = array_merge($this->config, $extras);

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
        return $this->sdk = new Client(
            $this->config['url'],
            $this->config['consumer_key'],
            $this->config['consumer_secret'],
            Arr::except($this->config, ['url', 'consumer_key', 'consumer_secret'])
        );
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
