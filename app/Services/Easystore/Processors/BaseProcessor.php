<?php

namespace App\Services\Easystore\Processors;

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
            ->wherePivot('meta->easystore_shop', $event->rawData->get('shop_domain'))
            ->first();
    }
}
