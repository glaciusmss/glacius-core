<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 9/17/2019
 * Time: 9:29 AM.
 */

namespace App\Contracts;

use App\Enums\MarketplaceEnum;
use App\Enums\NotificationChannelEnum;

interface Marketplace
{
    /**
     * @return NotificationChannelEnum|MarketplaceEnum
     */
    public function name();
}
