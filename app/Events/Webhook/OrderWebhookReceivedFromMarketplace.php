<?php

namespace App\Events\Webhook;

use Illuminate\Foundation\Events\Dispatchable;

class OrderWebhookReceivedFromMarketplace
{
    use Dispatchable;

    public $topic;
    public $rawData;
    public $marketplace;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($topic, $rawData, $marketplace)
    {
        $this->topic = $topic;
        $this->rawData = $rawData;
        $this->marketplace = $marketplace;
    }
}
