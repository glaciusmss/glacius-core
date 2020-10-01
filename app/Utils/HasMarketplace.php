<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/18/2019
 * Time: 9:56 AM.
 */

namespace App\Utils;

trait HasMarketplace
{
    protected $marketplace;

    /**
     * @param string $name
     * @return \App\Models\Marketplace
     */
    protected function getMarketplace(string $name)
    {
        if ($this->marketplace) {
            return $this->marketplace;
        }

        return $this->marketplace = \App\Models\Marketplace::whereName($name)->first();
    }
}
