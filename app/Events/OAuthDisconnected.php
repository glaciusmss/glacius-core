<?php

namespace App\Events;

use App\Enums\MarketplaceEnum;
use App\Shop;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OAuthDisconnected
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
