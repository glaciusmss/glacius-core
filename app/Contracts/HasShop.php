<?php

namespace App\Contracts;

use App\Models\Shop;

interface HasShop
{
    public function getShop(): Shop;

    public function setShop(Shop $shop);
}
