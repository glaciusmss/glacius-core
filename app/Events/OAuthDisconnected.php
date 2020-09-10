<?php

namespace App\Events;

use App\Enums\MarketplaceEnum;
use App\Shop;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OAuthDisconnected
{
    use Dispatchable, SerializesModels;

    public $shop;
    public $identifier;

    public function __construct(Shop $shop, string $identifier)
    {
        $this->shop = $shop;
        $this->identifier = $identifier;
    }
}
