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
use Illuminate\Contracts\Cache\Repository as CacheContract;

abstract class BaseMarketplace implements Marketplace
{
    use HasShop;

    protected $marketplace;
    protected $config;
    protected $cache;

    public function __construct($config, CacheContract $cache)
    {
        $this->config = $config;
        $this->cache = $cache;
    }

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
}
