<?php

namespace App\Events\Webhook;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomerCreateReceivedFromMarketplace
{
    use Dispatchable;

    public $rawData;
    public $marketplace;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($rawData, $marketplace)
    {
        $this->rawData = $rawData;
        $this->marketplace = $marketplace;
    }
}
