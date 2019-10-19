<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/19/2019
 * Time: 2:46 PM.
 */

namespace App\Services;


use App\Contracts\Marketplace;
use App\Enums\MarketplaceEnum;
use App\Utils\HasShop;

abstract class BaseMarketplace implements Marketplace
{
    use HasShop;

    protected $marketplace;

    /**
     * @return \App\Marketplace
     */
    protected function getMarketplace()
    {
        if ($this->marketplace) {
            return $this->marketplace;
        }

        $name = ($this->name() instanceof MarketplaceEnum) ? $this->name()->key : $this->name();

        return $this->marketplace = \App\Marketplace::whereName($name)->first();
    }

    protected function getConfig($key = null)
    {
        $name = ($this->name() instanceof MarketplaceEnum) ? $this->name()->value : $this->name();

        $config = config("marketplace.$name");

        if ($key === null) {
            return $config;
        }

        return $config[$key];
    }
}
