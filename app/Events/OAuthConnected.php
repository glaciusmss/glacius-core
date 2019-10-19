<?php

namespace App\Events;

use App\Enums\MarketplaceEnum;
use App\Shop;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OAuthConnected
{
    use Dispatchable, SerializesModels;

    public $shop;
    public $marketplace;

    public function __construct(Shop $shop, MarketplaceEnum $marketplace)
    {
        $this->shop = $shop;
        $this->marketplace = $marketplace;
    }
}
