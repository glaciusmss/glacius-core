<?php

namespace App\Events\Webhook;

use Illuminate\Foundation\Events\Dispatchable;

class OrderCreateReceivedFromMarketplace
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
