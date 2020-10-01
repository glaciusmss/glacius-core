<?php

namespace App\Events\Webhook;

use Illuminate\Foundation\Events\Dispatchable;

class WebhookReceived
{
    use Dispatchable;

    public $topic;
    public $rawData;
    public $identifier;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($topic, $rawData, $identifier)
    {
        $this->topic = $topic;
        $this->rawData = $rawData;
        $this->identifier = $identifier;
    }
}
