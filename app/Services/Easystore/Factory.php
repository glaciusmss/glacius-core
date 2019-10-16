<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/19/2019
 * Time: 12:08 PM.
 */

namespace App\Services\Easystore;


use App\Contracts\SdkFactory;
use EasyStore\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Factory implements SdkFactory
{
    protected $sdk;
    protected $config;

    public function setupSdk(array $config = null, array $extras = [])
    {
        if (!$this->config) {
            $this->config = $config ?? config('marketplace.easystore');

            $this->config['client_id'] = Arr::pull($this->config, 'key');
            $this->config['client_secret'] = Arr::pull($this->config, 'secret');
        }

        if (Arr::has($extras, 'easystoreShop')) {
            $easystoreShop = Arr::pull($extras, 'easystoreShop');
            $this->setEasystoreShop($easystoreShop);
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
        return $this->sdk = new Client($this->config);
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function clearCache()
    {
        $this->sdk = null;
    }

    protected function setEasystoreShop($easystoreShop)
    {
        if (Str::endsWith($easystoreShop, '/')) {
            $easystoreShop = substr($easystoreShop, 0, -1);
        }

        if (!Str::contains($easystoreShop, '.easy.co')) {
            $easystoreShop .= '.easy.co';
        }

        $this->config = array_merge($this->config, ['shop' => $easystoreShop]);

        return $this;
    }
}
