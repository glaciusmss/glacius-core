<?php

namespace App\Services\Woocommerce\Processors;

use App\Events\Webhook\WebhookReceived;
use App\Utils\HasMarketplace;
use App\Utils\UpdateAddresses;

class BaseProcessor
{
    use HasMarketplace, UpdateAddresses;

    protected $shop;

    protected function getShop(WebhookReceived $event)
    {
        if ($this->shop) {
            return $this->shop;
        }

        return $this->shop = $this->getMarketplace($event->identifier)
            ->shops()
            ->wherePivot('meta->woocommerce_store_url', $event->rawData->get('woocommerce_store_url'))
            ->first();
    }
}
